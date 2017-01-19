<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;


class LoadNavigationModuleCategory extends AbstractFixture implements OrderedFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'name' => 'Navigation',
        'title' => 'Menu',
        'slug' => 'navigation',
        'description' => 'Module pour le menu',
        'icon' => 'fa fa-bars',
        'nav' => true,
        'author' => 'S.Sumugan',
        'version' => '0.1',
        'update_available' => false,
        'access_level' => 4
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModuleCategory($manager);
    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 201;
    }
}