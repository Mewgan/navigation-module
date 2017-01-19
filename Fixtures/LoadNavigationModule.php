<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Services\LoadFixture;

class LoadNavigationModule extends AbstractFixture implements OrderedFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'module_simple_menu' => [
            'name' => 'Menu simple',
            'slug' => 'navigation',
            'callback' => 'Jet\Modules\Navigation\Controllers\FrontNavigationController@show',
            'description' => 'Affiche un menu simple',
            'category' => 'navigation',
            'access_level' => 4,
            'templates' => []
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModule($manager);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 202;
    }
}