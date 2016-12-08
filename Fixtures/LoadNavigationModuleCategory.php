<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\ModuleCategory;


class LoadNavigationModuleCategory extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        'name' => 'Navigation',
        'title' => 'Menu',
        'slug' => 'navigation',
        'description' => 'Module pour le menu',
        'icon' => 'fa fa-bars fa-4x',
        'author' => 'S.Sumugan',
        'version' => '0.1'
    ];

    public function load(ObjectManager $manager)
    {
        if(ModuleCategory::where('name',$this->data['name'])->where('author',$this->data['author'])->count() == 0) {
            $cat = new ModuleCategory();
            $cat->setName($this->data['name']);
            $cat->setTitle($this->data['title']);
            $cat->setSlug($this->data['slug']);
            $cat->setIcon($this->data['icon']);
            $cat->setAuthor($this->data['author']);
            $cat->setDescription($this->data['description']);
            $manager->persist($cat);
            $this->addReference($this->data['slug'], $cat);
            $manager->flush();
        }else{
            ModuleCategory::where('name',$this->data['name'])->set($this->data);
        }
    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}