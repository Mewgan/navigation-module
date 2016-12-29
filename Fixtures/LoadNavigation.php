<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\ModuleCategory;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;

class LoadNavigation extends AbstractFixture implements OrderedFixtureInterface
{

    private $data = [
        'menu' => [
            'name' => 'Menu',
            'website' => 'aster-society'
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $website =  Website::findOneByDomain($data['website']);
            $navigation = (Navigation::where('name',$data['name'])->where('website',$website)->count() == 0)
                ? new Navigation()
                : Navigation::findOneBy(['name' => $data['name'], 'website' => $website]);
            $navigation->setName($data['name']);
            $navigation->setWebsite($website);
            $this->addReference($key, $navigation);
            $this->addReference('website-' . $key, $website);
            $manager->persist($navigation);
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
        return 4;
    }
}