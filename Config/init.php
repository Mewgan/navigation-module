<?php

return [

    'app' => [
        'blocks' => [
            'NavigationModule' => [
                'path' => 'src/Modules/Navigation/',
                'namespace' => '\\Jet\\Modules\\Navigation',
                'view_dir' => 'src/Modules/Navigation/Views/',
                'prefix' => 'admin',
            ],
        ],
        'settings' => [
            'navigation' => [
                'page' => [
                    'id' => 'page',
                    'name' => 'Page',
                    'plural' => 'Pages',
                    'all' => '\\Jet\\AdminBlock\\Controllers\\PageController@listStaticPages',
                    'get_url' => '\\Jet\\AdminBlock\\Controllers\\PageController@getStaticPageUrl'
                ],
            ],
        ]
    ]
];