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

use Mautic\ApiBundle\Controller\CommonApiController;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;
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

    /**
     * Creates a new entity.
     *
     * @return Response
     */
    public function newEntityAction()
    {
        $parameters = $this->request->request->all();
        // handle reason code
        if (!isset($parameters['reason']) || empty($parameters['reason'])) {
            $parameters['reason'] = 3;
        }

        if (is_string($parameters['reason'])) {
            $parameters['reason'] = (int) $parameters['reason'];
        }
        $entity = $this->getNewEntity($parameters);

        if (!$this->checkEntityAccess($entity, 'create')) {
            return $this->accessDenied();
        }

        return $this->processForm($entity, $parameters, 'POST');
    }
}
