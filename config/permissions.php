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

        'store.list',
        'store.show',
        'store.create',
        'store.update',
        'store.delete',
        'store.force.token',
        'store.force.sync',
        'store.sync.url.update',
        'store.placeholder.update',

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
        'supervisor' => [
            'media.upload',

            'log.list',
            'store.list',
            'store.show',
            'store.create',
            'store.update',
            'store.delete',
            'store.force.sync',
            'store.placeholder.update',

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

            'campaign.activate',
            'campaign.cancel',
            'campaign.report',

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
            'media.upload',
            'store.list',
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

        'admin' => '*',

        'publicidad' => [
            'media.upload',

            'store.list',
            'store.show',
            'store.create',
            'store.update',
            'store.delete',
            'store.force.sync',
            'store.placeholder.update',

            'campaign.list',
            'campaign.show',
            'campaign.create',
            'campaign.update',
            'campaign.delete',
            'campaign.history.view',
            'campaign.history.restore',
            'campaign.history.clone',
            'campaign.history.calendar',
            'campaign.activate',
            'campaign.cancel',
            'campaign.report',
            
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
