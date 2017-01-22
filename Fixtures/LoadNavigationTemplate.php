<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Services\LoadFixture;

class LoadNavigationTemplate extends AbstractFixture
{
    use LoadFixture;

    protected $data = [
        'navigation_simple' => [
            'name' => 'ModuleNavigationPartialSimple',
            'title' => 'Menu simple',
            'content' => 'navigation',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ]
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadTemplate($manager);
    }
}