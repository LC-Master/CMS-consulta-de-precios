<?php

return [

    'permissions' => [
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',

        'reports.view',

        'campaigns.view',
        'campaigns.create',
        'campaigns.edit',
        'campaigns.delete',
    ],
    'roles' => [
        'admin' => '*',

        'mercadeo' => [
            'users.view',
            'reports.view',
            'campaigns.view',
            'campaigns.create',
            'campaigns.edit',
        ],

    ],
];
