<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\ModuleCategory;
use Jet\Models\Page;
use Jet\Models\Route;
use Jet\Modules\Navigation\Models\Navigation;
use Jet\Modules\Navigation\Models\NavigationItem;
use Jet\Modules\Post\Models\Post;
use Jet\Modules\Post\Models\PostCategory;

/**
 * Class LoadNavigationItem
 * @package Jet\Modules\Navigation\Fixtures
 */
class LoadNavigationItem extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * @var array
     */
    private $data = [
        [
            'title' => 'Accueil',
            'navigation' => 'menu',
            'parent' => null,
            'children' => [],
            'url' => '/',
            'route' => null,
            'type' => 'page',
            'type_id' => 'Accueil',
            'position' => 0,
        ],
        [
            'title' => 'Services',
            'navigation' => 'menu',
            'parent' => null,
            'children' => [],
            'url' => '/articles/service',
            'route' => 'module:post.type:dynamic.action:all',
            'type' => 'post_category',
            'type_id' => 'Service',
            'position' => 1,
        ],
        [
            'title' => 'Tarifs',
            'navigation' => 'menu',
            'parent' => null,
            'children' => [],
            'url' => '/tarifs',
            'route' => 'module:price.type:static.action:all',
            'type' => 'page',
            'type_id' => 'Tarifs',
            'position' => 2,
        ],
        [
            'title' => 'Contact',
            'navigation' => 'menu',
            'parent' => null,
            'children' => [],
            'url' => '/contact',
            'route' => 'module:contact.type:static.action:show',
            'type' => 'page',
            'type_id' => 'Contact',
            'position' => 3,
        ]
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
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
            /** @var Navigation $navigation */
            $navigation = $this->getReference($item['navigation']);
            /** @var Website $website */
            $website = $this->getReference('website-' . $item['navigation']);
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

        if (!is_null($value['type_id']))
            $item->setTypeId($this->getTypeId($value['type'], $value['type_id'], $website));
        $item->setPosition($value['position']);

        if (!is_null($value['route'])) {
            $route = Route::findOneBy(['name' => $value['route'], 'website' => $website]);
            if (is_null($route)) $route = Route::findOneBy(['name' => $value['route'], 'website' => null]);
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
        switch ($type){
            case 'page':
                $page = Page::findOneBy(['title' => $title, 'website' => $website]);
                return $page->getId();
                break;
            case 'post':
                $post = Post::findOneBy(['title' => $title, 'website' => $website]);
                return $post->getId();
                break;
            case 'post_category':
                $cat = PostCategory::findOneBy(['name' => $title, 'website' => $website]);
                return $cat->getId();
                break;
        }
        return null;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}