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

    'routes'       => [
        'main' => [
            'mautic_donotcontactextras_index'  => [
                'path'       => '/dnc/{page}',
                'controller' => 'MauticDoNotContactExtrasBundle:DoNotContactExtras:index',
            ],
            'mautic_donotcontactextras_view'   => [
                'path'       => '/dnc/view/{objectId}',
                'controller' => 'MauticDoNotContactExtrasBundle:DoNotContactExtras:view',
            ],
            'mautic_donotcontactextras_action' => [
                'path'         => '/dnc/{objectAction}/{objectId}',
                'controller'   => 'MauticDoNotContactExtrasBundle:DoNotContactExtras:execute',
                'requirements' => [
                    'objectAction' => '\w+',
                    'objectId'     => '\w+',
                ],
            ],
        ],
    ],
    'menu'         => [
        'main' => [
            'mautic.dnc' => [
                'route'     => 'mautic_donotcontactextras_index',
                'access'    => 'plugin:donotcontactextras:items:view',
                'id'        => 'mautic_donotcontactextras_root',
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
    'services'     => [
        'events' => [
            'mautic.donotcontactextras.subscriber.channel' => [
                'class'     => \MauticPlugin\MauticDoNotContactExtrasBundle\EventListener\ChannelSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
            'mautic.donotcontactextras.subscriber.dnc' => [
                'class'     => \MauticPlugin\MauticDoNotContactExtrasBundle\EventListener\DncGetSubscriber::class,
                'arguments' => [
                ],
            ],
        ],
        'models' => [
            'mautic.donotcontactextras.model.dnclistitem' => [
                'class' => 'MauticPlugin\MauticDoNotContactExtrasBundle\Model\DncListItemModel',
            ],
        ],
        'forms'  => [
            'mautic.donotcontactextras.form.type.dnclistitem' => [
                'class'     => 'MauticPlugin\MauticDoNotContactExtrasBundle\Form\Type\DncListItemType',
                'alias'     => 'dnclistitem',
                'arguments' => [
                    'mautic.security',
                    'mautic.lead.model.lead',
                ],
            ],
        ],
    ],
    'integrations' => [
        'mautic.dnc.integration' => [
            'class' => 'MauticPlugin\MauticDoNotContactExtrasBundle\Integration\DoNotContactExtrasIntegration',
        ],
    ],
];
