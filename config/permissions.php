<?php

return [

    'permissions' => [

        'log.list',

        'user.list',
        'user.show',
        'user.create',
        'user.update',
        'user.delete',
        'user.restore',

        'token.list',
        'token.create',
        'token.delete',

        'dashboard.view',

        'campaign.list',
        'campaign.show',
        'campaign.create',
        'campaign.update',
        'campaign.delete',

        'campaign.activate',
        'campaign.cancel',
        'campaign.report',

        'campaign.history.view',
        'campaign.history.restore',
        'campaign.history.clone',
        'campaign.history.calendar',


        'agreement.list',
        'agreement.show',
        'agreement.create',
        'agreement.update',
        'agreement.delete',

        'media.list',
        'media.show',
        'media.create',
        'media.update',
        'media.delete',

        'media.upload',

        'report.view',
        'report.generate',
    ],

    'roles' => [
        'admin' => [
            'media.upload',
            
            'log.list',

            'user.list',
            'user.show',
            'user.create',
            'user.update',
            'user.delete',
            'user.restore',

            'campaign.history.view',
            'campaign.history.restore',
            'campaign.history.clone',
            'campaign.history.calendar',
            
            'campaign.list',
            'campaign.show',
            'campaign.create',
            'campaign.update',
            'campaign.delete',

            'log.list',

            'agreement.list',
            'agreement.show',
            'agreement.create',
            'agreement.update',
            'agreement.delete',

            'dashboard.view',

            'media.list',
            'media.show',
            'media.create',
            'media.update',
            'media.delete',

            'report.view',
            'report.generate',
        ],

        'consultor' => [
            'log.list',
            'media.upload',

            'user.list',
            'user.show',

            'campaign.history.view',
            'campaign.history.calendar',
            
            'campaign.list',
            'campaign.show',

            'agreement.list',
            'agreement.show',
            'dashboard.view',

            'media.list',
            'media.show',

        ],

        'supervisor' => '*',

        'publicidad' => [
            'media.upload',

            'log.list',

            'campaign.list',
            'campaign.show',
            'campaign.create',
            'campaign.update',
            'campaign.delete',
            'campaign.history.view',
            'campaign.history.restore',
            'campaign.history.clone',
            'campaign.history.calendar',
            
            'agreement.list',
            'agreement.show',
            'agreement.create',
            'agreement.update',
            'agreement.delete',

            'media.list',
            'media.show',
            'media.create',
            'media.update',
            'media.delete',

            'dashboard.view',

            'report.view',
            'report.generate',
        ],
    ],
];
