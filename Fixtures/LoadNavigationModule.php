<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\Module;

class LoadNavigationModule extends AbstractFixture implements OrderedFixtureInterface
{

    private $data = [
        'module_simple_menu' => [
            'name' => 'Menu simple',
            'callback' => 'Jet\Modules\Navigation\Controllers\FrontNavigationController@show',
            'description' => 'Affiche un menu simple',
            'category' => 'navigation',
            'access_level' => 2,
            'templates' => [
            ]
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data){
            $module = (Module::where('callback',$data['callback'])->count() == 0)
                ? new Module()
                : Module::findOneByCallback($data['callback']);
            $module->setName($data['name']);
            $module->setCallback($data['callback']);
            $module->setDescription($data['description']);
            $module->setCategory($this->getReference($data['category']));
            $module->setAccessLevel($data['access_level']);
            $templates = new ArrayCollection();
            foreach ($data['templates'] as $template)
                $templates[] = $this->getReference($template);
            $module->setTemplates($templates);
            $this->addReference($key, $module);
            $manager->persist($module);
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
        return 2;
    }
}