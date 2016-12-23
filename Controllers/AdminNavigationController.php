<?php

namespace Jet\Modules\Navigation\Controllers;


use Jet\AdminBlock\Controllers\AdminController;
use Jet\Modules\Navigation\Models\Navigation;

class AdminNavigationController extends AdminController
{

    /**
     * @param $website
     * @return array
     */
    public function all($website){
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return ['resource' => Navigation::repo()->listAll($this->websites)];
    }

    /**
     * @param $id
     * @param $website
     * @return array
     */
    public function read($id, $website){
        /** @var Navigation $navigation */
        $navigation = Navigation::findOneById($id);
        if(!is_null($navigation)) {
            $website_id =$navigation->getWebsite()->getId();
            if ($this->getWebsite($website) && in_array($website_id, $this->websites)) {
                if(isset($this->website->getData()['parent_exclude']['navigations']) && in_array($website_id, $this->website->getData()['parent_exclude']['navigations']))
                    return ['status' => 'error', 'Le menu n\'existe pas'];
                return ['resource' => $navigation];
            }
            return ['status' => 'error', 'Impossible de trouver le site web'];
        }
        return ['status' => 'error', 'message' => 'Impossible de trouver le menu'];
    }

    public function create(){

    }

    public function update(){
        
    }
    
    public function createContent(){
        
    }

    public function updateContent(){

    }
    
}