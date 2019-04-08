<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle;

/**
 * Class DncEvents.
 *
 * Events available for DoNotContactExtrasBundle
 */
final class DncEvents
{
    /**
     * The mautic.dnc_post_delete event is dispatched after a DNC item is deleted.
     *
     * The event listener receives a MauticPlugin\MauticDoNotContactExtrasBundle\Event\DncEvent instance.
     *
     * @var string
     */
    const POST_DELETE = 'mautic.dnc_post_delete';

    /**
     * The mautic.dnc_post_save event is dispatched right after a DNC item is persisted.
     *
     * The event listener receives a MauticPlugin\MauticDoNotContactExtrasBundle\Event\DncEvent instance.
     *
     * @var string
     */
    const POST_SAVE = 'mautic.dnc_post_save';

    /**
     * The mautic.dnc_pre_delete event is dispatched before a DNC item is deleted.
     *
     * The event listener receives a MauticPlugin\MauticDoNotContactExtrasBundle\Event\DncEvent instance.
     *
     * @var string
     */
    const PRE_DELETE = 'mautic.dnc_pre_delete';

    /**
     * The mautic.dnc_pre_save event is dispatched right before a DNC item is persisted.
     *
     * The event listener receives a MauticPlugin\MauticDoNotContactExtrasBundle\Event\DncEvent instance.
     *
     * @var string
     */
    const PRE_SAVE = 'mautic.dnc_pre_save';
}
