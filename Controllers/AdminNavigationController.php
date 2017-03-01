<?php

namespace Jet\Modules\Navigation\Controllers;

use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Route;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;
use Jet\Modules\Navigation\Models\NavigationItem;
use Jet\Modules\Navigation\Requests\NavigationItemRequest;
use Jet\Modules\Navigation\Requests\NavigationRequest;
use Jet\Services\Auth;
use JetFire\Framework\Providers\EventProvider;

/**
 * Class AdminNavigationController
 * @package Jet\Modules\Navigation\Controllers
 */
class AdminNavigationController extends AdminController
{

    /**
     * @param $website
     * @return array
     */
    public function all($website)
    {
        if (!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return ['resource' => Navigation::repo()->listAll($this->websites, $this->getWebsiteData($this->website))];
    }

    /**
     * @param $website
     * @return array|mixed
     */
    public function getTypes($website)
    {
        if (!$this->getWebsite($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site'];

        $navigation = (!isset($this->app->data['app']['settings']['navigation'])) ? [] : $this->app->data['app']['settings']['navigation'];
        foreach ($navigation as $key => $type) {

            $callback = explode('@', $type['all']);
            $value = $this->callMethod($callback[0], $callback[1], ['website' => $website]);
            if (!isset($value['resource'])) return $value;
            $navigation[$key]['values'] = $value['resource'];

            if (isset($type['route'])) {
                $route = Route::repo()->getRouteByName($type['route'], $this->websites, $this->getWebsiteData($this->website));
                if (!is_null($route)) $navigation[$key]['route_id'] = $route['id'];
            }
        }
        return ['publication_types' => $navigation];
    }

    /**
     * @return array
     */
    public function getTypesName()
    {
        return (!isset($this->app->data['app']['settings']['navigation'])) ? [] : $this->app->data['app']['settings']['navigation'];
    }

    /**
     * @param $id
     * @param $website
     * @return array
     */
    public function read($id, $website)
    {
        /** @var Navigation $navigation */
        $navigation = Navigation::findOneById($id);
        if (!is_null($navigation)) {
            $website_id = $navigation->getWebsite()->getId();
            if ($this->getWebsite($website) && in_array($website_id, $this->websites)) {
                $data = $this->getWebsiteData($this->website);
                if (isset($data['parent_exclude']['navigations']) && in_array($navigation->getId(), $data['parent_exclude']['navigations']))
                    return ['status' => 'error', 'Le menu n\'existe pas'];
                return ['resource' => Navigation::repo()->read($id, ['websites' => $this->websites, 'options' => $this->getWebsiteData($this->website)])];
            }
            return ['status' => 'error', 'Impossible de trouver le site web'];
        }
        return ['status' => 'error', 'message' => 'Impossible de trouver le menu'];
    }

    /**
     * @param NavigationRequest $request
     * @param NavigationItemRequest $item_request
     * @param EventProvider $event
     * @param $website
     * @param $id
     * @return array|bool
     */
    public function updateOrCreate(NavigationRequest $request, NavigationItemRequest $item_request, EventProvider $event, $website, $id)
    {
        if ($request->method() == 'PUT' || $request->method() == 'POST') {
            $response = $request->validate();
            $replace = false;
            if ($response === true) {

                $website = Website::findOneById($website);
                if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

                if ($id == 'create') {
                    $navigation = new Navigation();
                    $nav_website = $website;
                } else {
                    $navigation = Navigation::findOneById($id);
                    $nav_website = $navigation->getWebsite();
                }

                if (!is_null($navigation)) {

                    if ($nav_website != $website && $id != 'create') {
                        $data = $this->excludeData($website->getData(), 'navigations', $navigation->getId());
                        $website->setData($data);
                        Website::watch($website);
                        $navigation = new Navigation();
                        $replace = true;
                    }
                    $navigation->setWebsite($website);

                    $value = $request->values();
                    $navigation->setName($value['name']);

                    $response = $this->updateNavigationItems($item_request, $navigation, $value['items'], $nav_website);

                    if (is_array($response)) return $response;

                    if (Navigation::watchAndSave($navigation)) {
                        $event->emit('updateNavigation', [$navigation->getId()]);
                        if ($replace) {
                            $website = $navigation->getWebsite();
                            $data = $this->replaceData($website->getData(), 'navigations', $id, $navigation->getId());
                            $website->setData($data);
                            Website::watchAndSave($website);
                        }
                        return ['status' => 'success', 'message' => 'Le menu a bien été mis à jour', 'resource' => Navigation::repo()->read($navigation->getId())];
                    }
                    return ['status' => 'error', 'message' => 'Le menu n\'a pas pu être mis à jour'];
                }
                return ['status' => 'error', 'message' => 'Impossible de trouver le menu'];
            }
            return $response;
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param NavigationItemRequest $item_request
     * @param Navigation $navigation
     * @param $items
     * @param $nav_website
     * @param null $parent
     * @return array|bool
     */
    private function updateNavigationItems(NavigationItemRequest $item_request, Navigation $navigation, $items, $nav_website, $parent = null)
    {
        foreach ($items as $value) {
            $item = (isset($value['id']) && substr($value['id'], 0, 6) != "create" && $nav_website == $navigation->getWebsite())
                ? NavigationItem::findOneById($value['id'])
                : new NavigationItem();
            $response = $this->updateItem($item_request, $navigation, $item, $nav_website, $value, $parent);
            if (is_array($response)) return $response;
        }
        return true;
    }

    /**
     * @param NavigationItemRequest $item_request
     * @param Navigation $navigation
     * @param NavigationItem $item
     * @param $nav_website
     * @param $value
     * @param null $parent
     * @return array|bool
     */
    private function updateItem(NavigationItemRequest $item_request, Navigation $navigation, NavigationItem $item, $nav_website, $value, $parent = null)
    {
        $response = $item_request->validate($item_request->rules(), $item_request::$messages, $value);
        if ($response === true) {
            if (empty($value['type_id'])) $value['type_id'] = null;
            if (empty($value['route']) || empty($value['route']['id'])) $value['route'] = null;

            $item->setTitle($value['title']);
            $item->setType($value['type']);
            $item->setParent($parent);
            $item->setTypeId((int)$value['type_id']);
            $item->setPosition((int)$value['position']);
            $item->setNavigation($navigation);

            if ($value['type'] != 'custom') {
                $navigation_types = $this->app->data['app']['settings']['navigation'];
                if (isset($navigation_types[$value['type']])) {
                    $callback = explode('@', $navigation_types[$value['type']]['get_url']);
                    if ($value['type'] == 'page') {
                        $route = $this->callMethod($callback[0], $callback[1], ['id' => $value['type_id']]);
                        if (is_array($route)) return $route;
                        $value['route'] = $route;
                        $value['url'] = $route->getUrl();
                    } else {
                        if (empty($value['route']) || !isset($value['route']['id']) || empty($value['route']['id']))
                            return ['status' => 'error', 'message' => 'La route n\'est pas définie pour :' . $value['title']];
                        $value['route'] = Route::findOneById($value['route']['id']);
                        if (is_null($value['route'])) return ['status' => 'error', 'message' => 'Impossible de trouver la route'];
                        $value['url'] = $this->callMethod($callback[0], $callback[1], ['url' => $value['route']->getUrl(), 'id' => $value['type_id']]);
                        if (is_array($value['url'])) return $value['url'];
                    }
                    $item->setRoute($value['route']);
                }
            }

            $item->setUrl($value['url']);

            if (isset($value['children']) && !empty($value['children'])) {
                $response = $this->updateNavigationItems($item_request, $navigation, $value['children'], $nav_website, $item);
                if (is_array($response)) return $response;
            }

            NavigationItem::watch($item);
            return true;
        }
        return $response;
    }

    /**
     * @param NavigationRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function delete(NavigationRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {
            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
            $data = $website->getData();

            if (!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces catégories'];

            $navigations = Navigation::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($navigations as $navigation) {
                if ($navigation['website']['id'] != $website->getId()) {
                    $data = $this->excludeData($data, 'navigations', $navigation['id']);
                } else
                    $ids[] = $navigation['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (Navigation::destroy($ids))
                ? ['status' => 'success', 'message' => 'Les menus ont bien été supprimées']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Les menus n\'ont pas pu être supprimées'];
    }

    /**
     * @param NavigationRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function deleteItem(NavigationRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {

            if (!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces catégories'];

            $items = Navigation::repo()->findItemsById($request->get('ids'));
            $ids = [];

            foreach ($items as $item)
                if ($item['navigation']['website']['id'] == $website) $ids[] = $item['id'];

            return (NavigationItem::destroy($ids))
                ? ['status' => 'success', 'message' => 'Les champs ont bien été supprimés']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }
}