<?php

namespace Jet\Modules\Navigation\Requests;

use JetFire\Framework\System\Request;

/**
 * Class PostRequest
 * @package Jet\Modules\Post\Requests
 */
class NavigationRequest extends Request
{

    /**
     * @var array
     */
    public static $messages = [
        'required' => 'Le champ ":field" doit être rempli',
    ];


    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name|items' => 'required',
        ];
    }

}