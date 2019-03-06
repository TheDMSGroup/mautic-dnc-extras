<?php
/**
 * Created by PhpStorm.
 * User: nbush
 * Date: 3/5/19
 * Time: 10:39 AM
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\EventListener;


use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Doctrine\ORM\NoResultException;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Entity\DoNotContact;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\LeadField;
use Mautic\LeadBundle\Event\LeadDNCGetCountEvent;
use Mautic\LeadBundle\Event\LeadDNCGetEntitiesEvent;
use Mautic\LeadBundle\Event\LeadDNCGetListEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;

/**
 * Class DncGetSubscriber
 * @package MauticPlugin\MauticDoNotContactExtrasBundle\EventListener
 */
class DncGetSubscriber extends CommonSubscriber
{

    /**
     * {@inherit_doc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::GET_DNC_COUNT    => ['getDncCount', 0],
            LeadEvents::GET_DNC_ENTITIES => ['getDncEntities', 0],
            LeadEvents::GET_DNC_LIST     => ['getDncList', 0],
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
     * Returns an array of lead field values to look for in the DNC list based on channel
     * @param Lead $lead
     * @param string $channel
     *
     * @return mixed
     */
    protected function getChannelFieldValues(Lead $lead, $channel)
    {
        $channelFieldValues = [];
        $type = 'email' === $channel ? 'email' : 'tel';

        $dbalQb = $this->em->getConnection()->createQueryBuilder();

        $leadFields = $dbalQb
            ->select('lf.alias')
            ->from('lead_fields', 'lf')
            ->where(
                $dbalQb->expr()->eq('lf.type', $type)
            )
            ->execute();

        /** @var LeadField $leadField */
        foreach($leadFields as $leadField) {
            if (null !== ($value = $lead->getFieldValue($leadField->getAlias()))) {
                $channelFieldValues[] = $value;
            }
        }

        return $channelFieldValues;
    }
 
    /**
     * @param LeadDNCGetEntitiesEvent $event
     */
    public function getDncEntities(LeadDNCGetEntitiesEvent $event)
    {
        $pluginEntities = [];

        $ormQb = $this->getDncItemRepository()->createQueryBuilder('dnc');

        $dncSearch = $this->getChannelFieldValues($event->getLead(), $event->getChannel());

        if (empty($dncSearch)) {
            return;
        }

        $ormQb->where(
            $ormQb->expr()->eq('dnc.channel', $event->getChannel()),
            $ormQb->expr()->in('dnc.name', $dncSearch)
        );

        if (false !== ($results = $ormQb->getQuery()->execute())) {
            /** @var DncListItem $result */
            foreach ($results as $result) {
                $fauxEntity= new DoNotContact();
                $fauxEntity->setChannel($event->getChannel());
                $fauxEntity->setLead($event->getLead());
                $fauxEntity->setReason($result->getReason();
                $fauxEntity->setDateAdded($result->getDateAdded());
                $pluginEntities[] = $fauxEntity;
            }
        }
        $event->addDNCEntities($pluginEntities);
    }

/**
 * TODO: WIP
 * @param LeadDNCGetCountEvent $event
 */
public function getDncCount(LeadDNCGetCountEvent $event)
{
    $count = 0;
    $dbalQb = $this->em->getConnection()->createQueryBuilder();
    // $count = $dbalQb->getFirstResult()[0];

    $event->setDNCCount($event->getDNCCount() + $count);
}
    /**
     * TODO: WIP
     * @param LeadDNCGetListEvent $event
     */
    public function getDncList(LeadDNCGetListEvent $event)
    {
        $dbalQb = $this->em->getConnection()->createQueryBuilder()
            ->from(MAUTIC_TABLE_PREFIX.'dnc_extra_list_items', 'dnc');
        ;

        /*            ->from(MAUTIC_TABLE_PREFIX.'lead_donotcontact', 'dnc')
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