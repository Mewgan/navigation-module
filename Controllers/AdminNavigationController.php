<?php

namespace Jet\Modules\Navigation\Controllers;


use Jet\AdminBlock\Controllers\AdminController;
use Jet\Modules\Navigation\Models\Navigation;

class AdminNavigationController extends AdminController
{

    public function all($website){
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return ['resource' => Navigation::repo()->listAll($this->websites)];
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