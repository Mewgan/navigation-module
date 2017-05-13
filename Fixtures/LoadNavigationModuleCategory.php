<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;


class LoadNavigationModuleCategory extends AbstractFixture
{
    use LoadFixture;

    protected $data = [
        'name' => 'Navigation',
        'title' => 'Menu',
        'slug' => 'navigation',
        'description' => 'Module pour le menu',
        'icon' => 'fa fa-bars',
        'author' => 'S.Sumugan',
        'version' => '0.1',
        'update_available' => false,
        'access_level' => 4
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModuleCategory($manager);
    }

}