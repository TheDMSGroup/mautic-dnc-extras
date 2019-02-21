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
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaAccountController.
 */
class DoNotContactExtrasController extends FormController
{
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
        return parent::viewStandard($objectId, 'dncListItem', 'plugin.dnc_extras');
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

            /** @var \MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DoNotContactListItem $item */
            $item = $args['viewParameters']['item'];

            // Setup page forms in session
            $order = [
                'date_added',
                'DESC',
            ];
            if ('POST' == $this->request->getMethod()) {
                $chartFilterValues = $this->request->request->has('media_chart')
                    ? $this->request->request->get('media_chart')
                    : $session->get('mautic.media.'.$item->getId().'.media_chart');
                if ($this->request->request->has('orderby')) {
                    $order[0] = $this->request->request->get('orderby');
                }
                if ($this->request->request->has('orderbydir')) {
                    $order[1] = $this->request->request->get('orderbydir');
                }
            } else {
                $chartFilterValues = $session->get('mautic.media.'.$item->getId().'.media_chart')
                    ? $session->get('mautic.media.'.$item->getId().'.media_chart')
                    : [
                        'date_from' => $this->get('mautic.helper.core_parameters')->getParameter(
                            'default_daterange_filter',
                            'midnight -1 month'
                        ),
                        'date_to'   => 'midnight tomorrow -1 second',
                        'type'      => '',
                    ];
            }

            $session->set('mautic.media.'.$item->getId().'.media_chart', $chartFilterValues);

            //Setup for the chart and stats datatable
            /** @var \MauticPlugin\MauticMediaBundle\Model\MediaAccountModel $model */
            $model = $this->getModel('media');

            $unit = $model->getTimeUnitFromDateRange(
                new \DateTime($chartFilterValues['date_from']),
                new \DateTime($chartFilterValues['date_to'])
            );

            $auditLog = $this->getAuditlogs($item);
            $stats    = $model->getStats(
                $item,
                $unit,
                new \DateTime($chartFilterValues['date_from']),
                new \DateTime($chartFilterValues['date_to']),
                isset($chartFilterValues['campaign']) ? $chartFilterValues['campaign'] : null
            );

            $chartFilterForm = $this->get('form.factory')->create(
                'media_chart',
                $chartFilterValues,
                [
                    'action' => $this->generateUrl(
                        'mautic_media_action',
                        [
                            'objectAction' => 'view',
                            'objectId'     => $item->getId(),
                        ]
                    ),
                ]
            );

            $args['viewParameters']['auditlog']        = $auditLog;
            $args['viewParameters']['stats']           = $stats;
            $args['viewParameters']['chartFilterForm'] = $chartFilterForm->createView();
            $args['viewParameters']['order']           = $order;

            unset($chartFilterValues['campaign']);
            $session->set('mautic.media.'.$item->getId().'.media_chart', $chartFilterValues);
        }

        return $args;
    }
}
