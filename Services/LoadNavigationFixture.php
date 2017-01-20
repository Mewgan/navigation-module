<?php

namespace Jet\Modules\Navigation\Services;


use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;

trait LoadNavigationFixture
{

    public function getNavigationContent($data)
    {
        if(isset($data['data']) && isset($data['data']['navigation']) && isset($data['website'])){
            $website = ($this->hasReference($data['website'])) ? $this->getReference($data['website']) : Website::findOneByDomain($data['website']);
            $nav = ($this->hasReference($data['data']['navigation']))
                ? $this->getReference($data['data']['navigation']) : Navigation::findOneBy(['name' => $data['data']['navigation'], 'website' => $website]);
            $data['data']['navigation'] = $nav->getId();
        }
        return $data;
    }
}