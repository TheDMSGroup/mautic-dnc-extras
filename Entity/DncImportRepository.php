<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * DncImportRepository.
 */
class DncImportRepository extends CommonRepository
{
    /**
     * Count how many imports with the status is there.
     *
     * @param float $ghostDelay when is the import ghost? In hours
     * @param int   $limit
     *
     * @return array
     */
    public function getGhostImports($ghostDelay = 2, $limit = null)
    {
        $dt = new \DateTime();

        list($hours, $partial) = explode('.', sprintf('%01.4f', $ghostDelay));

        if ($hours) {
            $dt->modify("-$hours hours");
        }

        if ($partial) {
            $partialDelay            = 60.0 * ($ghostDelay - $hours);
            list($minutes, $partial) = explode('.', sprintf('%01.4f', $partialDelay));

            if ($minutes) {
                $dt->modify("-$minutes minutes");
            }

            if ($partial) {
                $partialDelay            = 60.0 * ($partialDelay - $minutes);
                list($seconds, $partial) = explode('.', sprintf('%01.3f', $partialDelay));

                if ($seconds) {
                    $dt->modify("-$seconds seconds");
                }
            }
        }

        $q = $this->getQueryForStatuses([DncImport::IN_PROGRESS]);
        $q->select($this->getTableAlias())
            ->andWhere($q->expr()->lt($this->getTableAlias().'.dateModified', ':delay'))
            ->setParameter('delay', $dt);

        if (null !== $limit) {
            $q->setFirstResult(0)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getResult();
    }

    /**
     * Count how many imports with the status is there.
     *
     * @param array $statuses
     * @param int   $limit
     *
     * @return array
     */
    public function getImportsWithStatuses(array $statuses, $limit = null)
    {
        $q = $this->getQueryForStatuses($statuses);
        $q->select($this->getTableAlias())
            ->orderBy($this->getTableAlias().'.id', 'ASC')
            ->addOrderBy($this->getTableAlias().'.dateAdded', 'DESC');

        if (null !== $limit) {
            $q->setFirstResult(0)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getResult();
    }

    /**
     * Count how many imports with the status is there.
     *
     * @param array $statuses
     *
     * @return int
     */
    public function countImportsWithStatuses(array $statuses)
    {
        $q = $this->getQueryForStatuses($statuses);
        $q->select('COUNT(DISTINCT '.$this->getTableAlias().'.id) as theCount');

        $results = $q->getQuery()->getSingleResult();

        if (isset($results['theCount'])) {
            return (int) $results['theCount'];
        }

        return 0;
    }

    /**
     * @return int
     */
    public function countImportsInProgress()
    {
        return $this->countImportsWithStatuses([DncImport::IN_PROGRESS]);
    }

    public function getQueryForStatuses($statuses)
    {
        $q = $this->createQueryBuilder($this->getTableAlias());

        return $q->where($q->expr()->in($this->getTableAlias().'.status', $statuses));
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTableAlias()
    {
        return 'dnci';
    }
}
