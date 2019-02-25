<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\EventListener;

use Mautic\ChannelBundle\ChannelEvents;
use Mautic\ChannelBundle\Event\ChannelEvent;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\PluginBundle\Helper\IntegrationHelper;

/**
 * Class ChannelSubscriber.
 */
class ChannelSubscriber extends CommonSubscriber
{
    /**
     * @var IntegrationHelper
     */
    protected $helper;

    /**
     * ChannelSubscriber constructor.
     *
     * @param IntegrationHelper $helper
     */
    public function __construct(IntegrationHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ChannelEvents::ADD_CHANNEL => ['onAddChannel', 80],
        ];
    }

    /**
     * @param ChannelEvent $event
     */
    public function onAddChannel(ChannelEvent $event)
    {
        $integration = $this->helper->getIntegrationObject('DoNotContactExtras');
        if ($integration && $integration->getIntegrationSettings()->isPublished()) {
            $event->addChannel(
                'phone',
                [LeadModel::CHANNEL_FEATURE   => [],]
            );
            $event->addChannel(
                'sms',
                [LeadModel::CHANNEL_FEATURE   => [],]
            );
        }
    }
}
