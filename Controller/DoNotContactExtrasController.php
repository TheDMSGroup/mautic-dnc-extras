<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaAccountController.
 */
class DoNotContactExtrasController extends FormController
{

    public function __construct()
    {
        $this->setStandardParameters(
            'donotcontactextras.dnclistitem',
            'plugin:donotcontactextras:items',
            'donotcontactextras',
            'donotcontactextras',
            '',
            'MauticDoNotContactExtrasBundle:DncListItem',
            null,
            'donotcontactextras'
        );
    }

    /**
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction($page = 1)
    {
        // When the user inserts a numeric value, assume they want to find the entity by ID.
        $session = $this->get('session');
        $search  = $this->request->get('search', $session->get('mautic.'.$this->getSessionBase().'.filter', ''));
        if (isset($search) && is_numeric(trim($search))) {
            $search          = '%'.trim($search).'% OR ids:'.trim($search);
            $query           = $this->request->query->all();
            $query['search'] = $search;
            $this->request   = $this->request->duplicate($query);
            $session->set('mautic.'.$this->getSessionBase().'.filter', $search);
        } elseif (false === strpos($search, '%') && strlen($search) > 0 && false === strpos($search, 'OR ids:')) {
            $search          = '%'.trim($search, ' \t\n\r\0\x0B"%').'%';
            $search          = strpos($search, ' ') ? '"'.$search.'"' : $search;
            $query           = $this->request->query->all();
            $query['search'] = $search;
            $this->request   = $this->request->duplicate($query);
            $session->set('mautic.'.$this->getSessionBase().'.filter', $search);
        }

        return parent::indexStandard($page);
    }

    /**
     * Generates new form and processes post data.
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function newAction()
    {
        return parent::newStandard();
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param      $objectId
     * @param bool $ignorePost
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function editAction($objectId, $ignorePost = false)
    {
        return parent::editStandard($objectId, $ignorePost);
    }

    /**
     * Displays details on a DNC List Item.
     *
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function viewAction($objectId)
    {
        return parent::viewStandard($objectId, 'dncListItem', 'plugin.donotcontactextras');
    }

    /**
     * Clone an entity.
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        return parent::cloneStandard($objectId);
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        return parent::deleteStandard($objectId);
    }

    /**
     * Deletes a group of entities.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        return parent::batchDeleteStandard();
    }

    /**
     * @param $args
     * @param $view
     *
     * @return array
     *
     * @throws \Exception
     */
    public function customizeViewArguments($args, $view)
    {
        if ('view' == $view) {
            $session = $this->get('session');

            /** @var \MauticPlugin\MauticMediaBundle\Entity\DncListItem $item */
            $item = $args['viewParameters']['item'];

            $auditLog = $this->getAuditlogs($item);

            $args['viewParameters']['auditlog']        = $auditLog;
        }

        return $args;
    }

    protected function getModelName()
    {
        return 'donotcontactextras.dnclistitem';
    }

    /**
     * @param array $args
     * @param       $action
     *
     * @return array
     */
    protected function getPostActionRedirectArguments(array $args, $action)
    {
        $updateSelect = ('POST' == $this->request->getMethod())
            ? $this->request->request->get('donotcontactextras[updateSelect]', false, true)
            : $this->request->get(
                'updateSelect',
                false
            );
        if ($updateSelect) {
            switch ($action) {
                case 'new':
                case 'edit':
                    $passthrough             = $args['passthroughVars'];
                    $passthrough             = array_merge(
                        $passthrough,
                        [
                            'updateSelect' => $updateSelect,
                            'id'           => $args['entity']->getId(),
                            'name'         => $args['entity']->getName(),
                        ]
                    );
                    $args['passthroughVars'] = $passthrough;
                    $args['contentTemplate'] = 'MauticDoNotContactExtrasBundle:DoNotContactController:view';
                    break;
            }
        }

        return $args;
    }

    /**
     * @return array
     */
    protected function getEntityFormOptions()
    {
        $updateSelect = ('POST' == $this->request->getMethod())
            ? $this->request->request->get('donotcontactextras[updateSelect]', false, true)
            : $this->request->get(
                'updateSelect',
                false
            );
        if ($updateSelect) {
            return ['update_select' => $updateSelect];
        }
    }


    /**
     * Return array of options update select response.
     *
     * @param string $updateSelect HTML id of the select
     * @param object $entity
     * @param string $nameMethod   name of the entity method holding the name
     * @param string $groupMethod  name of the entity method holding the select group
     *
     * @return array
     */
    protected function getUpdateSelectParams(
        $updateSelect,
        $entity,
        $nameMethod = 'getName',
        $groupMethod = 'getLanguage'
    ) {
        $options = [
            'updateSelect' => $updateSelect,
            'id'           => $entity->getId(),
            'name'         => $entity->$nameMethod(),
        ];

        return $options;
    }

    /**
     * @param DncListItem $dncListItem
     * @param array|null   $filters
     * @param array|null   $orderBy
     * @param int          $page
     * @param int          $limit
     *
     * @return array
     */
    protected function getAuditlogs(
        DncListItem $dncListItem,
        array $filters = null,
        array $orderBy = null,
        $page = 1,
        $limit = 25
    ) {
        $session = $this->get('session');

        if (null == $filters) {
            $filters = $session->get(
                'mautic.donotcontactextras.'.$dncListItem->getId().'.auditlog.filters',
                [
                    'search'        => '',
                    'includeEvents' => [],
                    'excludeEvents' => [],
                ]
            );
        }

        if (null == $orderBy) {
            if (!$session->has('mautic.donotcontactextras.'.$dncListItem->getId().'.auditlog.orderby')) {
                $session->set('mautic.donotcontactextras.'.$dncListItem->getId().'.auditlog.orderby', 'al.dateAdded');
                $session->set('mautic.donotcontactextras.'.$dncListItem->getId().'.auditlog.orderbydir', 'DESC');
            }

            $orderBy = [
                $session->get('mautic.donotcontactextras.'.$dncListItem->getId().'.auditlog.orderby'),
                $session->get('mautic.donotcontactextras.'.$dncListItem->getId().'.auditlog.orderbydir'),
            ];
        }

        // Audit Log
        /** @var AuditLogModel $auditlogModel */
        $auditlogModel = $this->getModel('core.auditLog');

        $logs     = $auditlogModel->getLogForObject(
            'donotcontactextras',
            $dncListItem->getId(),
            $dncListItem->getDateAdded()
        );
        $logCount = count($logs);

        $types = [
            'delete'     => $this->translator->trans('mautic.donotcontactextras.event.delete'),
            'create'     => $this->translator->trans('mautic.donotcontactextras.event.create'),
            'identified' => $this->translator->trans('mautic.donotcontactextras.event.identified'),
            'ipadded'    => $this->translator->trans('mautic.donotcontactextras.event.ipadded'),
            'merge'      => $this->translator->trans('mautic.donotcontactextras.event.merge'),
            'update'     => $this->translator->trans('mautic.donotcontactextras.event.update'),
        ];

        return [
            'events'   => $logs,
            'filters'  => $filters,
            'order'    => $orderBy,
            'types'    => $types,
            'total'    => $logCount,
            'page'     => $page,
            'limit'    => $limit,
            'maxPages' => ceil($logCount / $limit),
        ];
    }

    /**
     * Get controller base if different than MauticDoNotContactExtrasBundle:DoNotContactExtras.
     *
     * @return string
     */
    protected function getControllerBase()
    {
        return 'MauticDoNotContactExtrasBundle:DoNotContactExtras';
    }
}
