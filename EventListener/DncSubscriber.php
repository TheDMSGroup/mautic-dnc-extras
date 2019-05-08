<?php

namespace MauticPlugin\MauticDoNotContactExtrasBundle\EventListener;

use Mautic\WebhookBundle\EventListener\WebhookSubscriberBase;
use MauticPlugin\MauticDoNotContactExtrasBundle\DncEvents;
use MauticPlugin\MauticDoNotContactExtrasBundle\Event\DncEvent;

class DncSubscriber extends WebhookSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DncEvents::POST_SAVE   => ['onDncPostSave', 0],
            DncEvents::POST_DELETE => ['onDncPostDelete', 0],
        ];
    }

    /**
     * @param DncEvent $event
     */
    public function onDncPostDelete(DncEvent $event)
    {
        $dncItem          = $event->getDncListItem();
        $lead             = $event->getLead();
        $payload          = [
            'dnc_item' => $dncItem,
            'lead'     => $lead,
            'action' => 'DELETED',
        ];
        $webhookEvents = $this->getEventWebooksByType(DncEvents::POST_DELETE);

        $this->webhookModel->queueWebhooks($webhookEvents, $payload);
    }

    /**
     * @param DncEvent $event
     */
    public function onDncPostSave(DncEvent $event)
    {
        $dncItem          = $event->getDncListItem();
        $lead             = $event->getLead();
        $payload          = [
            'dnc_item' => $dncItem,
            'lead'     => $lead,
            'action' => 'CREATED_UPDATED',
        ];
        $webhookEvents = $this->getEventWebooksByType(DncEvents::POST_SAVE);

        $this->webhookModel->queueWebhooks($webhookEvents, $payload);
    }
}
