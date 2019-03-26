<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;

/**
 * Class DncEvent.
 */
class DncEvent extends CommonEvent
{
    /**
     * @param DncListItem $entity
     * @param bool|false  $isNew
     */
    public function __construct(DncListItem $entity, $isNew = false)
    {
        $this->entity = $entity;
        $this->isNew  = $isNew;
    }

    /**
     * Returns the DncListItem entity.
     *
     * @return DncListItem
     */
    public function getDncListItem()
    {
        return $this->entity;
    }

    /**
     * Sets the DncListItem entity.
     *
     * @param DncListItem $entity
     */
    public function setDncListItem(DncListItem $entity)
    {
        $this->entity = $entity;
    }
}
