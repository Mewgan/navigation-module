<?php

namespace Jet\Modules\Navigation\Controllers;


use Jet\FrontBlock\Controllers\MainController;
use Jet\Models\Website;
use Jet\Modules\Navigation\Models\Navigation;

class FrontNavigationController extends MainController
{

    /**
     * @param Website $website
     * @param $content
     * @return null
     */
    public function show(Website $website, $content){
        $data = $content->getData();
        if(!empty($data) && isset($data['navigation']) && is_numeric($data['navigation'])) {
            if(empty($this->websites)) {
                $this->websites[] = $website;
                $this->getThemeWebsites($website);
            }
            $navigation = Navigation::repo()->renderFront($data['navigation'], ['websites' => $this->websites, 'exclude' => $website->getData()]);
            return $this->_renderContent($content->getTemplate(), 'src/Modules/Navigation/Views/', compact('navigation'));
        }
        return null;
    }

}