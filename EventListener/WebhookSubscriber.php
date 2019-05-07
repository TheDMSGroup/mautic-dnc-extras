<?php

namespace MauticPlugin\MauticDoNotContactExtrasBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\WebhookBundle\Event\WebhookBuilderEvent;
use Mautic\WebhookBundle\WebhookEvents;
use MauticPlugin\MauticDoNotContactExtrasBundle\DncEvents;

class WebhookSubscriber extends CommonSubscriber
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WebhookEvents::WEBHOOK_ON_BUILD => ['onWebhookBuild', 0],
        ];
    }

    /**
     * Add event triggers and actions.
     *
     * @param WebhookBuilderEvent $event
     */
    public function onWebhookBuild(WebhookBuilderEvent $event)
    {
        $event->addEvent(DncEvents::POST_SAVE, [
            'label'       => 'DNC Item Created/Updated',
        ]);
        $event->addEvent(DncEvents::POST_DELETE, [
            'label' => 'DNC Item Deleted',
        ]);
    }
}
