<?php

namespace Jet\Modules\Navigation\Models;

use Doctrine\ORM\QueryBuilder;
use Jet\Models\AppRepository;

/**
 * Class NavigationRepository
 * @package Jet\Modules\Navigation\Models
 */
class NavigationRepository extends AppRepository
{

    /**
     * @param $websites
     * @param array $options
     * @return array
     */
    public function listAll($websites, $options = [])
    {

        $query = Navigation::queryBuilder();

        $query->select('partial n.{id,name}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Navigation\Models\Navigation', 'n')
            ->leftJoin('n.website', 'w');

        $query = $this->getQueryWithParams($query, ['websites' => $websites, 'options' => $options]);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param $id
     * @param array $params
     * @param int $level
     * @return array
     */
    public function read($id, $params = [], $level = 2)
    {

        $query = Navigation::queryBuilder();

        /* Query */
        $query->select('n')
            ->addSelect('partial w.{id,domain}')
            ->addSelect('partial p.{id}')
            ->addSelect('partial i0.{id,title,url,type,type_id,position}')
            ->addSelect('partial r0.{id,url}')
            ->from('Jet\Modules\Navigation\Models\Navigation', 'n')
            ->leftJoin('n.website', 'w')
            ->leftJoin('n.items', 'i0')
            ->leftJoin('i0.parent', 'p')
            ->leftJoin('i0.route', 'r0');

        $query->where($query->expr()->eq('n.id', ':id'))
            ->setParameter('id', $id)
            ->andWhere($query->expr()->isNull('p.id'));

        $query = $this->getQueryWithParams($query, $params);

        $query->orderBy('n.id', 'DESC')
            ->addOrderBy('i0.position', 'ASC');

        for ($i = 1; $i <= $level; ++$i) {
            $query->addSelect('partial i' . $i . '.{id,title,url,type,type_id,position}')
                ->leftJoin('i' . ($i - 1) . '.children', 'i' . $i)
                ->leftJoin('i' . $i . '.route', 'r' . $i)
                ->addSelect('partial r' . $i . '.{id,url}')
                ->addOrderBy('i' . $i . '.position', 'ASC');
        }
        $result = $query->getQuery()->getArrayResult();
        return (isset($result[0])) ? $result[0] : null;
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids = [])
    {
        $query = Navigation::queryBuilder()
            ->select('partial n.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Navigation\Models\Navigation', 'n')
            ->leftJoin('n.website', 'w');
        return $query->where($query->expr()->in('n.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param $ids
     * @return array
     */
    public function findItemsById($ids = [])
    {
        $query = NavigationItem::queryBuilder()
            ->select('partial i.{id}')
            ->addSelect('partial n.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Navigation\Models\NavigationItem', 'i')
            ->leftJoin('i.navigation', 'n')
            ->leftJoin('n.website', 'w');
        return $query->where($query->expr()->in('i.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param $type
     * @param $type_id
     * @param $params
     * @return array
     */
    public function findItemsByWebsite($type, $type_id, $params)
    {
        $query = Navigation::queryBuilder()
            ->select('i')
            ->from('Jet\Modules\Navigation\Models\NavigationItem', 'i')
            ->leftJoin('i.navigation', 'n')
            ->leftJoin('n.website', 'w');

        $query->where($query->expr()->eq('i.type', ':type'))
            ->andWhere($query->expr()->eq('i.type_id', ':type_id'))
            ->setParameter('type', $type)
            ->setParameter('type_id', $type_id);

        $query = $this->getQueryWithParams($query, $params);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $id
     * @param array $params
     * @param int $level
     * @return array
     */
    public function renderFront($id, $params = [], $level = 2)
    {

        $query = Navigation::queryBuilder();

        /* Query */
        $query->select('n')
            ->addSelect('partial i0.{id,title,url,position}')
            ->from('Jet\Modules\Navigation\Models\Navigation', 'n')
            ->leftJoin('n.items', 'i0')
            ->leftJoin('n.website', 'w')
            ->leftJoin('i0.parent', 'p');

        if (isset($params['options']['parent_replace']) && isset($params['options']['parent_replace']['navigations']) && !empty($params['options']['parent_replace']['navigations']) && isset($params['options']['parent_replace']['navigations'][$id])) {
            $query->where($query->expr()->eq('n.id', ':id'))
                ->setParameter('id', $params['options']['parent_replace']['navigations'][$id]);
        } else {
            $query->where($query->expr()->eq('n.id', ':id'))
                ->setParameter('id', $id);
        }

        $query->andWhere($query->expr()->isNull('p.id'));

        $query = $this->getQueryWithParams($query, $params);

        $query->orderBy('n.id', 'DESC')
            ->addOrderBy('i0.position', 'ASC');

        for ($i = 1; $i <= $level; ++$i) {
            $query->addSelect('partial i' . $i . '.{id,title,url,position}')
                ->leftJoin('i' . ($i - 1) . '.children', 'i' . $i)
                ->addOrderBy('i' . $i . '.position', 'ASC');
        }

        $result = $query->getQuery()->getArrayResult();
        return (isset($result[0])) ? $result[0] : null;
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return QueryBuilder
     */
    public function getQueryWithParams(QueryBuilder $query, $params)
    {
        if (isset($params['websites']) && !empty($params['websites'])) {
            $query->andWhere($query->expr()->in('w.id', ':websites'))
                ->setParameter('websites', $params['websites']);
        }

        if (isset($params['options'])){
            $query = $this->excludeData($query, $params['options'], 'navigations');
        }
        
        return $query;
    }


} 