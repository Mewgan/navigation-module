<?php

namespace Jet\Modules\Navigation\Services;


use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Page;
use Jet\Models\Route;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;
use Jet\Modules\Navigation\Models\NavigationItem;

trait LoadNavigationFixture
{

    protected $navigation_type_callback = [
        'page' => 'getPageTypeId'
    ];

    /**
     * @param $key
     * @param $value
     */
    public function addNavigationTypeCallback($key, $value)
    {
        $this->navigation_type_callback[$key] = $value;
    }


    /**
     * @param ObjectManager $manager
     */
    public function loadNavigation(ObjectManager $manager)
    {
        foreach ($this->data as $key => $data) {
            $website = ($this->hasReference($data['website'])) ? $this->getReference($data['website']) : Website::findOneByDomain($data['website']);
            $navigation = (Navigation::where('name', $data['name'])->where('website', $website)->count() == 0)
                ? new Navigation()
                : Navigation::findOneBy(['name' => $data['name'], 'website' => $website]);
            $navigation->setName($data['name']);
            $navigation->setWebsite($website);
            $this->setReference($key, $navigation);
            $manager->persist($navigation);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadNavigationItem(ObjectManager $manager)
    {
        $this->setItems($this->data, $manager);
        $manager->flush();
    }

    /**
     * @param $items
     * @param $manager
     * @param null $parent
     */
    private function setItems($items, $manager, $parent = null){
        foreach ($items as $key => $item) {
            /** @var Website $website */
            $website = ($this->hasReference($item['website'])) ? $this->getReference($item['website']) : Website::findOneByDomain($item['website']);
            /** @var Navigation $navigation */
            $navigation = ($this->hasReference($item['navigation'])) ? $this->getReference($item['navigation']) : Navigation::findOneBy(['name' => $item['navigation'], 'website' => $website]);
            
            $navigationItem = (NavigationItem::where('title', $item['title'])->where('navigation', $navigation)->count() == 0)
                ? new NavigationItem()
                : NavigationItem::findOneBy(['title' => $item['title'], 'navigation' => $navigation]);
            $this->setRecursive($navigationItem, $navigation, $parent, $item, $website, $manager);
        }
    }

    /**
     * @param NavigationItem $item
     * @param Navigation $navigation
     * @param null $parent
     * @param $value
     * @param $website
     * @param ObjectManager $manager
     */
    private function setRecursive(NavigationItem $item, Navigation $navigation, $parent = null, $value, $website, ObjectManager $manager)
    {
        $item->setTitle($value['title']);
        $item->setUrl($value['url']);
        $item->setType($value['type']);
        $item->setPosition($value['position']);
        
        if (!is_null($value['type_id']))
            $item->setTypeId($this->getTypeId($value['type'], $value['type_id'], $website));

        if (!is_null($value['route'])) {
            $route = ($this->hasReference($value['route'])) ? $this->getReference($value['route']) : Route::findOneBy(['name' => $value['route'], 'website' => $website]);
            $item->setRoute($route);
        }

        $item->setParent($parent);
        $item->setNavigation($navigation);

        $this->setItems($value['children'], $manager, $item);

        $manager->persist($item);
    }

    /**
     * @param $type
     * @param $title
     * @param $website
     * @return null
     */
    private function getTypeId($type, $title, $website){
        if($this->hasReference($title)) return $this->getReference($title)->getId();
        if(isset($this->navigation_type_callback[$type]))
            return call_user_func_array([$this,$this->navigation_type_callback[$type]], [$title, $website]);
        return null;
    }

    /**
     * @param $title
     * @param $website
     * @return mixed
     */
    protected function getPageTypeId($title, $website){
        /** @var Page $page */
        $page = $this->hasReference($title) ? $this->getReference($title) : Page::findOneBy(['title' => $title, 'website' => $website]);
        return $page->getId();
    }

    /**
     * @param $data
     * @param Website $website
     * @return mixed
     */
    public function getNavigationContent($data, Website $website)
    {
        if (isset($data['data']) && isset($data['data']['navigation']) && isset($data['website'])) {
            $nav = ($this->hasReference($data['data']['navigation']))
                ? $this->getReference($data['data']['navigation']) : Navigation::findOneBy(['name' => $data['data']['navigation'], 'website' => $website]);
            $data['data']['navigation'] = $nav->getId();
        }
        return $data;
    }


    /**
     * @param $data
     * @return array
     */
    protected function getCustomFieldNavigation($data)
    {
        $new_content = $data;
        $website = (isset($new_content['website']) && $this->hasReference($new_content['website'])) ? $this->getReference($new_content['website']) : null;
        foreach ($data['content'] as $key => $item) {
            if (is_array($item)) {
                $content = [];
                $this->recursiveSetNav($item, $content, $website);
                $new_content['content'][$key] = $content;
            } elseif(!empty($item)) {
                $nav = explode('@', $item);
                $new_content['content'][$key] = $nav[0] . '@' . $this->getTypeId($nav[0], $nav[1], $website);
            }else{
                $new_content['content'][$key] = $item;
            }
        }
        return $new_content;
    }

    /**
     * @param $items
     * @param array $content
     * @param $website
     */
    private function recursiveSetNav($items, &$content = [], $website){
        foreach ($items as $index => $nav) {
            if(is_array($nav)){
                $content[$index] = [];
                $this->recursiveSetPost($nav, $content[$index], $website);
            } elseif(!empty($nav)) {
                $nav = explode('@', $nav);
                $content[] = $nav[0] . '@' . $this->getTypeId($nav[0], $nav[1], $website);
            }else{
                $content[] = $nav;
            }
        }
    }

}