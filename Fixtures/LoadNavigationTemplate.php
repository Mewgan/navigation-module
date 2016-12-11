<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\Template;

class LoadNavigationTemplate extends AbstractFixture implements OrderedFixtureInterface
{

    private $data = [

    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data){
            $template = (Template::where('name',$data['name'])->count() == 0)
                ? new Template()
                : Template::findOneByName($data['name']);
            $template->setName($data['name']);
            $template->setTitle($data['title']);
            $template->setContent($data['content']);
            $template->setCategory($data['category']);
            $template->setScope($data['scope']);
            $template->setType($data['type']);
            $this->addReference($key, $template);
            $manager->persist($template);

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
        return 3;
    }
}