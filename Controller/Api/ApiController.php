<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Controller\Api;

use FOS\RestBundle\Util\Codes;
use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\CampaignBundle\Entity\Campaign;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ApiController.
 */
class ApiController extends CommonApiController
{

    /**
     * @param FilterControllerEvent $event
     *
     * @return mixed|void
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->model            = $this->getModel('donotcontactextras.dnclistitem');
        $this->entityClass      = DncListItem::class;
        $this->entityNameOne    = 'dnclistitem';
        $this->entityNameMulti  = 'dnclistitems';
        $this->serializerGroups = [
            'dnclistitemDetails',
            'categoryList',
            'publishDetails',
        ];

        // Prevent excessive writes to the users table.
        define('MAUTIC_ACTIVITY_CHECKED', 1);

        parent::initialize($event);
    }


}
