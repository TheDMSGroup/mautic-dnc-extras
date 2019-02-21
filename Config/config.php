<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'Do Not Contact Extras',
    'description' => 'Adds a custom DNC entity and list, by channel',
    'version'     => '1.0',
    'author'      => 'Mautic',

    'routes'   => [
        'main' => [
            'mautic_dnc_extras_index'         => [
                'path'       => '/dnc/{page}',
                'controller' => 'MauticDoNotContactExtrasBundle:DoNotContactExtras:index',
            ],
            // 'mautic_dnc_extras_action'        => [
            //     'path'         => '/dnc/{objectAction}/{objectId}',
            //     'controller'   => 'MauticDoNotContactExtrasBundle:DoNotContactExtras:execute',
            //     'requirements' => [
            //         'objectAction' => '\w+',
            //         'objectId'     => '\w+',
            //     ],
            // ],
        ],
    ],
    'menu' => [
        'main' => [
            'mautic.dnc' => [
                'route'     => 'mautic_dnc_extras_index',
                'access'    => 'plugin:dnc_extras:items:view',
                'id'        => 'mautic_dnc_extras_root',
                'iconClass' => 'fa-ban',
                'priority'  => 15,
                'checks'    => [
                    'integration' => [
                        'DoNotContactExtras' => [
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'services' => [
        'events' => [
            // 'mautic.dnc.dashboard.subscriber' => [
            //     'class'     => 'MauticPlugin\MauticHealthBundle\EventListener\DashboardSubscriber',
            //     'arguments' => [
            //         'mautic.health.model.health',
            //     ],
            // ],
        ],
    ],
    'integrations' => [
        'mautic.dnc.integration' => [
            'class' => 'MauticPlugin\MauticDoNotContactExtrasBundle\Integration\DoNotContactExtrasIntegration',
        ],
    ],
];
