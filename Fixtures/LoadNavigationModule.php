<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Services\LoadFixture;

class LoadNavigationModule extends AbstractFixture implements DependentFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'module_simple_menu' => [
            'name' => 'Menu simple',
            'slug' => 'navigation',
            'callback' => 'Jet\Modules\Navigation\Controllers\FrontNavigationController@show',
            'description' => 'Affiche un menu simple',
            'category' => 'navigation',
            'access_level' => 4
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModule($manager);
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            'Jet\Modules\Navigation\Fixtures\LoadNavigationModuleCategory'
        ];
    }
}