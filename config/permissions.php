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
        'dashboard.view',
        'campaigns.list',
        'campaigns.show',
        'campaigns.create',
        'campaigns.update',
        'campaigns.delete',
        'campaigns.history',
        'campaigns.history.calendar',
        'agreements.list',
        'agreements.show',
        'agreements.create',
        'agreements.update',
        'agreements.delete',
        'logs.list',
        'medias.list',
        'medias.show',
        'medias.create',
        'medias.update',
        'medias.delete',

        'reports.view',
    ],

    'roles' => [
        'admin' => [
            'users.list',
            'users.show',
            'users.create',
            'users.update',
            'users.delete',
            'campaigns.history',
            'campaigns.history.calendar',
            'campaigns.list',
            'campaigns.show',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'logs.list',
            'agreements.list',
            'agreements.show',
            'agreements.create',
            'agreements.update',
            'agreements.delete',
            'dashboard.view',

            'medias.list',
            'medias.show',
            'medias.create',
            'medias.update',
            'medias.delete',

            'reports.view',
        ],

        'consultor' => [
            'users.list',
            'users.show',
            'campaigns.history',
            'campaigns.history.calendar',
            'campaigns.list',
            'campaigns.show',

            'agreements.list',
            'agreements.show',
            'dashboard.view',

            'medias.list',
            'medias.show',

            'reports.view',
        ],

        'supervisor' => '*',

        'publicidad' => [
            'campaigns.list',
            'campaigns.show',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'campaigns.history',
            'campaigns.history.calendar',
            'agreements.list',
            'agreements.show',
            'agreements.create',
            'agreements.update',
            'agreements.delete',
            'logs.list',
            'medias.list',
            'medias.show',
            'medias.create',
            'medias.update',
            'medias.delete',
            'dashboard.view',

            'reports.view',
        ],
    ],
];
