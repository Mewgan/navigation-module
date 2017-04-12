<?php

return [

    '/module/navigation/update-url' => [
        'use' => 'ApiNavigationController@updateUrl',
        'name' => 'api.navigation.update_url',
        'method' => 'POST'
    ],

    '/module/navigation/*' => [
        'use' => 'AdminNavigationController@{method}',
        'ajax' => true
    ],
];