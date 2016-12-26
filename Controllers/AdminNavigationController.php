<?php

namespace Jet\Modules\Navigation\Controllers;


use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;
use Jet\Modules\Navigation\Models\NavigationItem;
use Jet\Modules\Navigation\Requests\NavigationItemRequest;
use Jet\Modules\Navigation\Requests\NavigationRequest;

class AdminNavigationController extends AdminController
{

    /**
     * @param $website
     * @return array
     */
    public function all($website){
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return ['resource' => Navigation::repo()->listAll($this->websites)];
    }

    /**
     * @param $id
     * @param $website
     * @return array
     */
    public function read($id, $website){
        /** @var Navigation $navigation */
        $navigation = Navigation::findOneById($id);
        if(!is_null($navigation)) {
            $website_id = $navigation->getWebsite()->getId();
            if ($this->getWebsite($website) && in_array($website_id, $this->websites)) {
                if(isset($this->website->getData()['parent_exclude']['navigations']) && in_array($website_id, $this->website->getData()['parent_exclude']['navigations']))
                    return ['status' => 'error', 'Le menu n\'existe pas'];
                return ['resource' => $navigation];
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
    public function createOrUpdate(NavigationRequest $request, NavigationItemRequest $item_request, $website, $id){
        if ($request->method() == 'PUT' || $request->method() == 'POST') {
            $response = $request->validate();
            if ($response === true) {
                $navigation = ($id == 'create') ? new Navigation() : Navigation::findOneById($id);
                if (!is_null($navigation)) {
                    $website = Website::findOneById($website);
                    if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

                    $value = $request->values();

                    if ($id == 'create') {
                        $navigation->setWebsite($website);
                    } elseif ($navigation->getWebsite() != $website) {
                        $data = $website->getData();
                        $data['parent_exclude']['navigations'] = (isset($data['parent_exclude']['navigations']))
                            ? $data['parent_exclude']['navigations'] : [];
                        if (!in_array($navigation->getId(), $data['parent_exclude']['navigations']))
                            $data['parent_exclude']['navigations'][] = $navigation->getId();
                        $website->setData($data);
                        Website::watch($website);
                        $navigation = new Navigation();
                        $navigation->setWebsite($website);
                    }

                    $navigation->setName($value['name']);
                    $response = $this->updateNavigationItems($item_request);
                    if(is_array($response))return $response;

                    $response = (Navigation::watchAndSave($navigation))
                        ? ['status' => 'success', 'message' => 'Les champs ont bien été mis à jour', 'resource' => $navigation]
                        : ['status' => 'error', 'message' => 'Les champs n\'ont pas pu être mis à jour'];
                    return $response;
                }
            }
            return $response;
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    private function updateNavigationItems(NavigationItemRequest $item_request, Navigation $navigation, $items, $nav_website){
        foreach ($items as $value){
            $item = (isset($value['id']) && substr( $value['id'], 0, 6 ) != "create" && $nav_website == $navigation->getWebsite())
                ? NavigationItem::findOneById($value['id'])
                : new NavigationItem();
            $response = $this->updateItem($navigation, $nav_website, $item, $value);
            if(is_array($response)) return $response;
        }
        return true;
    }

    private function updateItem(){

    }
}