<?php

namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;

class LoadNavigationWebsiteModule extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        'aster-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'balsamine-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'heliotrope-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'pivoine-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'rose-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'luffy-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'zoro-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'sanji-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'chopper-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
        'robin-society' => [
            'modules' => [
                'module_simple_menu',
            ],
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $website = Website::findOneByDomain($key);
            foreach ($data['modules'] as $module) {
                $mod = $this->getReference($module);
                $modules = is_null($website->getModules())?[]:$website->getModules();
                if(!in_array($mod->getId(),$modules))
                    $website->addModule($mod->getId());
            }
            if(isset($data['exclude'])){
                $d = $website->getData();
                $d['parent_exclude']['grid_editors'] = (isset($d['parent_exclude']['grid_editors']))
                    ? array_merge($data['exclude'],$d['parent_exclude']['grid_editors'])
                    : $data['exclude'];
                $website->setData($d);
            }
            $manager->persist($website);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 7;
    }
}