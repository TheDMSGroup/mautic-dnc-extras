<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItemRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ExportController.
 */
class ExportController extends CommonController
{
    /**
     * @param string $channel
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|StreamedResponse
     *
     * @throws \Exception
     */
    public function exportAction($channel = '')
    {
        $start = 0;
        $limit = 1000;

        if (!$this->get('mautic.security')->hasEntityAccess(
            'donotcontactextras:items:own',
            'donotcontactextras:items:view',
            0
        )) {
            return $this->accessDenied();
        }
        $entityManager = $this->get('doctrine.orm.entity_manager');

        /** @var DncListItemRepository $dncRepository */
        $dncRepository = $this->getModel('donotcontactextras.dnclistitem')->getRepository();

        $fileName = 'DNCExport'.(new \DateTime())->format('Y-m-d').'.csv';

        $response = new StreamedResponse(
            function () use (
                $dncRepository,
                $entityManager,
                $start,
                $limit,
                $channel
            ) {
                ini_set('max_execution_time', 0);
                $handle = fopen('php://output', 'w');

                $fields = [
                    'ID',
                    $channel ? ucwords($channel) : 'Value',
                    'Reason',
                    'Added',
                    'Description',
                ];
                if (!$channel) {
                    $fields[] = 'Channel';
                }
                fputcsv($handle, $fields);
                do {
                    $items = $dncRepository->getPublishedByChannel($channel, $start, $limit);
                    /**
                     * @var
                     * @var $item DncListItem
                     */
                    foreach ($items as $id => $item) {
                        $fields = [
                            $item->getId(),
                            $item->getName(),
                            $this->translator->trans('mautic.dnc.reason.'.$item->getReason()),
                            $item->getDateAdded()->format(\DateTime::ISO8601),
                            $item->getDescription(),
                        ];
                        if (!$channel) {
                            $fields[] = $item->getChannel();
                        }
                        fputcsv($handle, $fields);
                    }
                    $entityManager->flush();
                    $entityManager->clear();
                    $start += count($items);
                } while ($items);

                fclose($handle);
            },
            200,
            [
                'Content-Type'        => 'application/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ]
        );

        return $response;
    }
}
