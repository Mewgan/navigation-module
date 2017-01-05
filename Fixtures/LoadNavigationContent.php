<?php

namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Content;
use Jet\Models\Page;
use Jet\Models\Section;
use Jet\Models\Website;

class LoadNavigationContent extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        /* Aster website navigation module content */
        'aster_navigation_content' => [
            'name' => 'Menu',
            'block' => 'navigation',
            'website' => 'aster-society',
            'module' => 'module_simple_menu',
            'template' => 'navigation_simple',
            'section' => null,
            'page' => null,
            'data' => [
                'class' => '',
                'navigation' => '1'
            ]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $website = Website::findOneByDomain($data['website']);
            $content = (Content::where('website',$website)->where('block',$data['block'])->where('name',$data['name'])->count() == 0)
                ?  new Content()
                : Content::findOneBy(['website' => $website, 'block' => $data['block'], 'name' => $data['name']]);
            $content->setName($data['name']);
            $content->setBlock($data['block']);
            if(!is_null($data['page']))$content->setPage(Page::findOneById($data['page']));
            $content->setWebsite($website);
            $content->setModule($this->getReference($data['module']));
            $content->setTemplate($this->getReference($data['template']));
            if (!is_null($data['section']))
                $content->setSection(Section::findOneById($data['section']));
            $content->setData($data['data']);
            $this->setReference($key, $content);
            $manager->persist($content);
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
        return 208;
    }
}