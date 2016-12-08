<?php
namespace Jet\Modules\Navigation\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\ModuleCategory;

class LoadNavigation extends AbstractFixture implements OrderedFixtureInterface
{

    private $data = [
    
    ];

    public function load(ObjectManager $manager)
    {
      
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}