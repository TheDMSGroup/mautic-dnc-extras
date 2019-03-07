<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Controller\AjaxLookupControllerTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    use AjaxLookupControllerTrait;

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getImportProgressAction(Request $request)
    {
        $dataArray = ['success' => 1];

        if ($this->get('mautic.security')->isGranted('donotcontactextras:items:create')) {
            $session               = $this->get('session');
            $dataArray['progress'] = $session->get('mautic.dnc.import.progress', [0, 0]);
            $dataArray['percent']  = ($dataArray['progress'][1]) ? ceil(($dataArray['progress'][0] / $dataArray['progress'][1]) * 100) : 100;
        }

        return $this->sendJsonResponse($dataArray);
    }
}
