<?php

return [

    '/module/navigation/*' => [
        'use' => 'AdminNavigationController@{method}',
        'ajax' => true
    ],
];