<?php
/**
 * Created by PhpStorm.
 * User: nbush
 * Date: 3/5/19
 * Time: 10:39 AM.
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Entity\DoNotContact;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Event\LeadDNCGetEntitiesEvent;
use Mautic\LeadBundle\LeadEvents;

//use Mautic\LeadBundle\Event\LeadDNCGetCountEvent;
//use Mautic\LeadBundle\Event\LeadDNCGetEntitiesEvent;
//use Mautic\LeadBundle\Event\LeadDNCGetListEvent;

/**
 * Class DncGetSubscriber.
 */
class DncGetSubscriber extends CommonSubscriber
{
    /**
     * {@inherit_doc}.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::GET_DNC_COUNT    => ['onGetDncCount', 0],
            LeadEvents::GET_DNC_ENTITIES => ['onGetDncEntities', 0],
            LeadEvents::GET_DNC_LIST     => ['onGetDncList', 0],
        ];
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItemRepository
     */
    protected function getDncItemRepository()
    {
        return $this->em->getRepository('MauticDoNotContactExtrasBundle:DncListItem');
    }

    /**
     * Returns an array of lead field values to look for in the DNC list based on channel.
     *
     * @param Lead   $lead
     * @param string $channel
     *
     * @return mixed
     */
    protected function getChannelFieldValues(Lead $lead, $channel)
    {
        $channelFieldValues = [];

        switch ($channel) {
            case 'email':
                if ($lead->getEmail()) {
                    $channelFieldValues[] = $lead->getEmail();
                }
                break;

            case 'sms':
            case 'phone':
            default:
                if ($lead->getPhone()) {
                    $channelFieldValues[] = $lead->getPhone();
                }
                if ($lead->getMobile()) {
                    $channelFieldValues[] = $lead->getMobile();
                }
        }

        return $channelFieldValues;
    }

    /**
     * @param \Mautic\LeadBundle\Event\LeadDNCGetEntitiesEvent $event
     */
    public function onGetDncEntities(LeadDNCGetEntitiesEvent $event)
    {
        $lead    =  $event->getLead();
        $channel = $event->getChannel();

        $dncSearch = $this->getChannelFieldValues($lead, $channel);
        if (empty($dncSearch)) {
            return;
        }

        $dbalQb = $this->em->getConnection()->createQueryBuilder()
            ->select('dnc.reason, dnc.date_added')
            ->from('dnc_extras_list_items', 'dnc')
            ->where('dnc.channel = :channel')
            ->setParameter('channel', $channel);

        if (1 < count($dncSearch)) {
            $dbalQb->andWhere(
                $dbalQb->expr()->in('dnc.name', $dncSearch)
            );
        } else {
            $dbalQb->andWhere('dnc.name = :search')
            ->setParameter('search', $dncSearch[0]);
        }

        if (false !== ($results = $dbalQb->execute())) {
            foreach ($results as $result) {
                $fauxEntity = new DoNotContact();
                $fauxEntity->setChannel($channel);
                $fauxEntity->setLead($lead);
                $fauxEntity->setReason($result['reason']);
                $fauxEntity->setDateAdded(new \DateTime($result['date_added']));
                $pluginEntities[] = $fauxEntity;
            }
            unset($results);
        }
        if (!empty($pluginEntities)) {
            $event->addDNCEntities($pluginEntities);
        }

        return $event;
    }

    /**
     * TODO: WIP.
     *
     * @param LeadDNCGetCountEvent $event
     */
    public function onGetDncCount($event)
    {
        $count  = 0;
        //$dbalQb = $this->em->getConnection()->createQueryBuilder();
        // $count = $dbalQb->getFirstResult()[0];
        $event->setDNCCount($event->getDNCCount() + $count);
    }

    /**
     * TODO: WIP.
     *
     * @param LeadDNCGetListEvent $event
     */
    public function onGetDncList($event)
    {
        $dbalQb = $this->em->getConnection()->createQueryBuilder()
            ->from(MAUTIC_TABLE_PREFIX.'dnc_extra_list_items', 'dnc');

        /*
            ->from(MAUTIC_TABLE_PREFIX.'lead_donotcontact', 'dnc')
            ->leftJoin('dnc', MAUTIC_TABLE_PREFIX.'leads', 'l', 'l.id = dnc.lead_id');
        if (null === $event->getChannel()) {
            $q->select('dnc.channel, dnc.reason, l.id as lead_id');
        } else {
            $q->select('l.id, dnc.reason')
              ->where('dnc.channel = :channel')
              ->setParameter('channel', $$event->getChannel());
        }

        if ($contacts) {
            $q->andWhere(
                $q->expr()->in('l.id', $contacts)
            );
        }

        $results = $q->execute()->fetchAll();

        $dnc = [];
        foreach ($results as $r) {
            if (isset($r['lead_id'])) {
                if (!isset($dnc[$r['lead_id']])) {
                    $dnc[$r['lead_id']] = [];
                }

                $dnc[$r['lead_id']][$r['channel']] = $r['reason'];
            } else {
                $dnc[$r['id']] = $r['reason'];
            }
        }
*/
    }
}
