<?php

namespace Jet\Modules\Navigation\Controllers;

use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Page;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;
use Jet\Modules\Navigation\Models\NavigationItem;
use Jet\Modules\Post\Models\Post;
use Jet\Modules\Post\Models\PostCategory;
use JetFire\Framework\System\Request;

/**
 * Class ApiNavigationController
 * @package Jet\Modules\Navigation\Controllers
 */
class ApiNavigationController extends AdminController
{

    private $callbacks = [
        'page' => 'getPageUrl'
    ];

    /**
     * @param Request $request
     */
    public function updateUrl(Request $request)
    {
        if ($request->has('page')) {
            $content = Page::findOneById($request->get('page'));
            if (!is_null($content)) {
                $old_content = $request->has('old_page') ? Page::findOneById($request->get('old_page')) : $content;
                if(!is_null($old_content)) $this->updateNav($request, $content, $old_content, 'page');
            }
        } elseif ($request->has('post')) {
            $content = Post::findOneById($request->get('post'));
            if (!is_null($content)) {
                $old_content = $request->has('old_post') ? Post::findOneById($request->get('old_post')) : $content;
                if(!is_null($old_content)) $this->updateNav($request, $content, $old_content, 'post');
            }
        } elseif ($request->has('post_category')) {
            $content = PostCategory::findOneById($request->get('post_category'));
            if (!is_null($content)) {
                $old_content = $request->has('old_post_category') ? PostCategory::findOneById($request->get('old_post_category')) : $content;
                if(!is_null($old_content)) $this->updateNav($request, $content, $old_content, 'post_category');
            }
        }
    }

    /**
     * @param Request $request
     * @param $content
     * @param $old_content
     * @param $type
     */
    private function updateNav(Request $request, $content, $old_content, $type)
    {
        if ($request->has('website')) {
            $website = $request->get('website');
            $this->getWebsite($website);
            if(!is_null($this->website)) {
                $data = $this->getWebsiteData($this->website);
                $items = Navigation::repo()->findItemsByWebsite($type, $old_content->getId(), ['websites' => $this->websites, 'options' => $data]);
                /** @var NavigationItem $item */
                foreach ($items as $item) {
                    $nav = $item->getNavigation();
                    $nav_website = $nav->getWebsite()->getId();
                    if (in_array($nav_website, $this->websites)) {
                        if ($nav_website == $website) {
                            $callback = (isset($this->callbacks[$type])) ? $this->callbacks[$type] : 'getUrl';
                            $response = call_user_func_array([$this, $callback], [$item, $item, $content]);
                            if ($response == false) exit;
                            $item->setRoute($response['route']);
                            $item->setUrl($response['url']);
                            NavigationItem::watch($item);
                        } else {
                            $new_nav = new Navigation();
                            $new_nav->setName($nav->getName());
                            $new_nav->setWebsite($this->website);
                            $this->createItems($nav, $new_nav, $type, $old_content->getId(), $content);
                            Navigation::watchAndSave($new_nav);
                            $data = $this->excludeData($data, 'navigations', $nav->getId());
                            $data = $this->replaceData($data, 'navigations', $nav->getId(), $new_nav->getId());
                            break;
                        }
                    }
                }
                $this->website->setData($data);
                Website::watchAndSave($this->website);
            }
        }
    }

    /**
     * @param Navigation $old_nav
     * @param Navigation $nav
     * @param $type
     * @param $type_id
     * @param $content
     */
    private function createItems(Navigation $old_nav, Navigation $nav, $type, $type_id, $content)
    {
        $items = $old_nav->getItems();
        /** @var NavigationItem $item */
        foreach ($items as $item) {
            $new_item = new NavigationItem();
            $new_item->setNavigation($nav);
            $new_item->setParent($item->getParent());
            $new_item->setTitle($item->getTitle());
            $new_item->setType($item->getType());
            $new_item->setPosition($item->getPosition());

            if ($item->getType() == $type && $item->getTypeId() == $type_id) {
                $callback = (isset($this->callbacks[$type])) ? $this->callbacks[$type] : 'getUrl';
                $response = call_user_func_array([$this, $callback], [$item, $new_item, $content]);
                if ($response == false) exit;
                $new_item->setRoute($response['route']);
                $new_item->setUrl($response['url']);
                $new_item->setTypeId($content->getId());
            }else{
                $new_item->setRoute($item->getRoute());
                $new_item->setUrl($item->getUrl());
                $new_item->setTypeId($item->getTypeId());
            }

            NavigationItem::watch($new_item);
        }
    }

    /**
     * @param NavigationItem $old_item
     * @param NavigationItem $item
     * @param Page $page
     * @return array | boolean
     */
    protected function getPageUrl(NavigationItem $old_item, NavigationItem $item, Page $page)
    {
        if (is_null($page)) return false;
        $item->setRoute($page->getRoute());
        $item->setUrl($page->getRoute()->getUrl());
        return ['route' => $page->getRoute(), 'url'=> $page->getRoute()->getUrl() ];
    }

    /**
     * @param NavigationItem $old_item
     * @param NavigationItem $item
     * @param $content
     * @return array | boolean
     */
    protected function getUrl(NavigationItem $old_item, NavigationItem $item, $content)
    {
        if (is_null($content)) return ['status' => 'error', 'message' => 'Impossible de trouver l\'article'];
        $replaces = ['id', 'slug'];
        $url = $old_item->getRoute()->getUrl();
        foreach ($replaces as $replace)
            $url = str_replace(':' . $replace, call_user_func([$content, 'get'.ucwords($replace)]), $url);
        return ['route' => $old_item->getRoute(), 'url' => $url];
    }

}