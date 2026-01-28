<?php

return [

    'permissions' => [

        'logs.list',

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

        'campaigns.activate',
        'campaigns.cancel',
        'campaigns.report',

        'campaigns.history.view',
        'campaigns.history.restore',
        'campaigns.history.clone',
        'campaigns.history.calendar',


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

        'media.upload',

        'reports.view',
        'reports.generate',
    ],

    'roles' => [
        'admin' => [
            'media.upload',

            'logs.list',
            'users.list',
            'users.show',
            'users.create',
            'users.update',
            'users.delete',
            'campaigns.history.view',
            'campaigns.history.restore',
            'campaigns.history.clone',
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
            'reports.generate',
        ],

        'consultor' => [
            'logs.list',
            'media.upload',

            'users.list',
            'users.show',
            'campaigns.history.view',
            'campaigns.history.calendar',
            'campaigns.list',
            'campaigns.show',

            'agreements.list',
            'agreements.show',
            'dashboard.view',

            'medias.list',
            'medias.show',

        ],

        'supervisor' => '*',

        'publicidad' => [
            'media.upload',

            'logs.list',

            'campaigns.list',
            'campaigns.show',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'campaigns.history.view',
            'campaigns.history.restore',
            'campaigns.history.clone',
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
            'reports.generate',
        ],
    ],
];
