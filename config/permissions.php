<?php

return [

    'permissions' => [
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',

        'tokens.view',
        'tokens.create',
        'tokens.edit',
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

        'publicidad' => [
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
