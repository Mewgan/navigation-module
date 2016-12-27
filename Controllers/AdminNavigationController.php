<?php

namespace Jet\Modules\Navigation\Controllers;


use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Page;
use Jet\Models\Route;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;
use Jet\Modules\Navigation\Models\NavigationItem;
use Jet\Modules\Navigation\Requests\NavigationItemRequest;
use Jet\Modules\Navigation\Requests\NavigationRequest;

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
        return ['resource' => Navigation::repo()->listAll($this->websites, $this->website->getData())];
    }

    /**
     * @param $website
     * @return array|mixed
     */
    public function getTypes($website)
    {
        $navigation = (!isset($this->app->data['app']['settings']['navigation'])) ? [] : $this->app->data['app']['settings']['navigation'];
        foreach ($navigation as $key => $type) {
            $callback = explode('@', $type['all']);
            $value = $this->callMethod($callback[0], $callback[1], [$website]);
            if (!isset($value['resource'])) return $value;
            $navigation[$key]['values'] = $value['resource'];
        }
        return ['publication_types' => $navigation];
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
                if (isset($this->website->getData()['parent_exclude']['navigations']) && in_array($website_id, $this->website->getData()['parent_exclude']['navigations']))
                    return ['status' => 'error', 'Le menu n\'existe pas'];
                return ['resource' => Navigation::repo()->read($id, ['websites' => $this->websites, 'exclude' => $this->website->getData()])];
            }
            return ['status' => 'error', 'Impossible de trouver le site web'];
        }
        return ['status' => 'error', 'message' => 'Impossible de trouver le menu'];
    }

    /**
     * @param NavigationRequest $request
     * @param NavigationItemRequest $item_request
     * @param $website
     * @param $id
     * @return array|bool
     */
    public function updateOrCreate(NavigationRequest $request, NavigationItemRequest $item_request, $website, $id)
    {
        if ($request->method() == 'PUT' || $request->method() == 'POST') {
            $response = $request->validate();
            if ($response === true) {
                $navigation = ($id == 'create') ? new Navigation() : Navigation::findOneById($id);
                $nav_website = $navigation->getWebsite();
                if (!is_null($navigation)) {
                    $website = Website::findOneById($website);
                    if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

                    $value = $request->values();

                    if ($nav_website != $website) {
                        $data = $website->getData();
                        $data['parent_exclude']['navigations'] = (isset($data['parent_exclude']['navigations']))
                            ? $data['parent_exclude']['navigations'] : [];
                        if (!in_array($navigation->getId(), $data['parent_exclude']['navigations']))
                            $data['parent_exclude']['navigations'][] = $navigation->getId();
                        $website->setData($data);
                        Website::watch($website);
                        $navigation = new Navigation();
                    }

                    $navigation->setName($value['name']);
                    $navigation->setWebsite($website);
                    $response = $this->updateNavigationItems($item_request, $navigation, $value['items'], $nav_website);

                    if (is_array($response)) return $response;

                    $response = (Navigation::watchAndSave($navigation))
                        ? ['status' => 'success', 'message' => 'Les champs ont bien été mis à jour', 'resource' => $navigation]
                        : ['status' => 'error', 'message' => 'Les champs n\'ont pas pu être mis à jour'];
                    return $response;
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
            if (empty($value['route'])) $value['route'] = null;

            $item->setTitle($value['title']);
            $item->setType($value['type']);
            $item->setParent($parent);
            $item->setTypeId((int)$value['type_id']);
            $item->setPosition((int)$value['position']);
            $item->setNavigation($navigation);

            if ($value['type'] != 'custom' && $value['type'] != 'page' && $value['route'] != null) {
                $navigation_types = $this->app->data['app']['settings']['navigation'];
                if (isset($navigation_types[$value['type']])) {
                    $callback = explode('@', $navigation_types[$value['type']]['get_url']);
                    $value['route'] = Route::findOneById($value['route']['id']);
                    if (is_null($value['route'])) return ['status' => 'error', 'message' => 'Impossible de trouver la route'];
                    $value['url'] = ($value['type'] == 'page')
                        ? $this->callMethod($callback[0], $callback[1], [$value['type_id']])
                        : $this->callMethod($callback[0], $callback[1], [$value['route']->getUrl(), $value['type_id']]);
                    if (is_array($value['url'])) return $value['url'];
                }
            }

            $item->setRoute($value['route']);
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
}