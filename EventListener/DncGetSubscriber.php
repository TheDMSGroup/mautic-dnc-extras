<?php
/**
 * Created by PhpStorm.
 * User: nbush
 * Date: 3/5/19
 * Time: 10:39 AM
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\EventListener;


use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadDNCGetCountEvent;
use Mautic\LeadBundle\Event\LeadDNCGetEntitiesEvent;
use Mautic\LeadBundle\Event\LeadDNCGetListEvent;
use Mautic\LeadBundle\LeadEvents;

class DncGetSubscriber extends CommonSubscriber
{

    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::GET_DNC_COUNT    => ['getDncCount', 0],
            LeadEvents::GET_DNC_ENTITIES => ['getDncEntities', 0],
            LeadEvents::GET_DNC_LIST     => ['getDncList', 0],
        ];
    }

    public function getDncCount(LeadDNCGetCountEvent $event)
    {

    }

    public function getDncEntities(LeadDNCGetEntitiesEvent $event)
    {

    }

    public function getDncList(LeadDNCGetListEvent $event)
    {

    }

}