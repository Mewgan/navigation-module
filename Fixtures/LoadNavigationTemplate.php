<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\Template;

class LoadNavigationTemplate extends AbstractFixture implements OrderedFixtureInterface
{

    private $data = [
        'navigation_simple' => [
            'name' => 'ModuleNavigationPartialSimple',
            'title' => 'Menu simple',
            'content' => 'navigation',
            'website' => null,
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ],
        /* Aster template */
        'aster_navigation_partial' => [
            'name' => 'ThemeAsterNavigationFilePartial',
            'title' => 'Theme Aster Navigation Template',
            'content' => 'Aster/Views/navigation',
            'website' => 'Aster Website',
            'category' => 'partial',
            'scope' => 'specified',
            'type' => 'file'
        ],
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
            if(!is_null($data['website']))$template->setWebsite($this->getReference($data['website']));
            $this->setReference($key, $template);
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
        return 203;
    }
}