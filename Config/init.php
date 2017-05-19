<?php

return [

    'app' => [
        'Navigation' => [
            'order' => 0,
            'hook' => [
                'left_sidebar' => true
            ],
            'routes' => [
                [
                    'title' => 'Menu',
                    'name'=> 'module:navigation',
                ]
            ]
        ],
        'blocks' => [
            'NavigationModule' => [
                'path' => 'src/Modules/Navigation/',
                'namespace' => '\\Jet\\Modules\\Navigation',
                'view_dir' => 'src/Modules/Navigation/Views/',
                'prefix' => 'admin',
            ],
        ],
        'fixtures' => [
            'src/Modules/Navigation/Fixtures/'
        ],
        'settings' => [
            'custom_field_type' => [
                'content' => [
                    'values' => [
                        ['navigation', 'Navigation' , 'Navigation@navigationCustomField' , 'Navigation@navigationRenderCustomField']
                    ]
                ]
            ],
            'custom_field_callback' => [
                'navigation' => '\\Jet\\Modules\\Navigation\\Controllers\\FrontNavigationController@renderField'
            ],
            'navigation' => [
                'page' => [
                    'id' => 'page',
                    'name' => 'Page',
                    'plural' => 'Pages',
                    'all' => '\\Jet\\AdminBlock\\Controllers\\PageController@listStaticPages',
                    'get_url' => '\\Jet\\AdminBlock\\Controllers\\PageController@getStaticPageRoute'
                ],
            ],
        ],
        'events' => [
            'updatePage' => [
                [
                    'type' => 'async', // linear or async
                    'method' => 'POST',
                    'route' => 'api.navigation.update_url',
                    //'callback' => '\\Jet\\Events\\NavigationListener@updateNavigationUrl'
                ]
            ]
        ]
    ]
];