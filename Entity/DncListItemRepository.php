<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital DoNotContactExtras Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * Class DncListItemRepository.
 */
class DncListItemRepository extends CommonRepository
{
    /**
     * Get a list of entities.
     *
     * @param array $args
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getEntities(array $args = [])
    {
        $alias = $this->getTableAlias();

        $q = $this->_em
            ->createQueryBuilder()
            ->select($alias)
            ->from('MauticDoNotContactExtrasBundle:DncListItem', $alias, $alias.'.id');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTableAlias()
    {
        return 'dli';
    }

    /**
     * @return array
     */
    public function getSearchCommands()
    {
        return $this->getStandardSearchCommands();
    }

    /**
     * @return array
     */
    public function getList($currentId)
    {
        $alias = $this->getTableAlias();
        $q     = $this->createQueryBuilder($this->getTableAlias());
        $q->select('partial '.$alias.'.{id, value, channel, reason}')->orderBy(
            $alias.'.id', 'DESC'
        );

        return $q->getQuery()->getArrayResult();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\DBAL\Query\QueryBuilder $q
     * @param                                                              $filter
     *
     * @return array
     */
    protected function addCatchAllWhereClause($q, $filter)
    {
        $alias = $this->getTableAlias();

        return $this->addStandardCatchAllWhereClause(
            $q,
            $filter,
            [
                $alias.'.value',
                $alias.'.channel',
                $alias.'.reason',
            ]
        );
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\DBAL\Query\QueryBuilder $q
     * @param                                                              $filter
     *
     * @return array
     */
    protected function addSearchCommandWhereClause($q, $filter)
    {
        return $this->addStandardSearchCommandWhereClause($q, $filter);
    }

    /**
     * @return array
     */
    protected function getDefaultOrder()
    {
        return [
            [$this->getTableAlias().'.id', 'DESC'],
        ];
    }
}
