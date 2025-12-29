<?php

return [

    'permissions' => [

        'users.list',
        'users.show',
        'users.create',
        'users.update',
        'users.delete',

        'tokens.list',
        'tokens.create',
        'tokens.delete',

        'campaigns.list',
        'campaigns.show',
        'campaigns.create',
        'campaigns.update',
        'campaigns.delete',

        'agreements.list',
        'agreements.show',
        'agreements.create',
        'agreements.update',
        'agreements.delete',

        'medias.list',
        'medias.show',
        'medias.create',
        'medias.update',
        'medias.delete',

        'reports.view',
    ],

    'roles' => [
        'admin' => '*',

        'publicity' => [
            'campaigns.list',
            'campaigns.show',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',

            'agreements.list',
            'agreements.show',
            'agreements.create',
            'agreements.update',
            'agreements.delete',

            'medias.list',
            'medias.show',
            'medias.create',
            'medias.update',
            'medias.delete',

            'reports.view',
        ],
    ],
];
