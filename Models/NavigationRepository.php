<?php

namespace Jet\Modules\Navigation\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class NavigationRepository
 * @package Jet\Modules\Navigation\Models
 */
class NavigationRepository extends EntityRepository
{

    /**
     * @param $websites
     * @param array $excludes
     * @return array
     */
    public function listAll($websites, $excludes = [])
    {

        $query = Navigation::queryBuilder();

        $query->select('partial n.{id,name}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Navigation\Models\Navigation', 'n')
            ->leftJoin('n.website', 'w');

        $query = $this->getQueryParams($query,['websites' => $websites, 'exclude' => $excludes]);

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

        $query = $this->getQueryParams($query,$params);

        $query->orderBy('n.id', 'DESC')
            ->addOrderBy('i0.position', 'ASC');

        for($i = 1; $i <= $level; ++$i){
            $query->addSelect('partial i'.$i.'.{id,title,url,type,type_id,position}')
                ->leftJoin('i'.($i-1).'.children', 'i'.$i)
                ->leftJoin('i'.($i-1).'.route', 'r'.$i)
                ->addSelect('partial r'.$i.'.{id,url}')
                ->addOrderBy('i'.$i.'.position', 'ASC');
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return QueryBuilder
     */
    public function getQueryParams(QueryBuilder $query, $params){
        if(isset($params['websites']) && !empty($params['websites'])) {
            $query->andWhere($query->expr()->in('w.id', ':websites'))
                ->setParameter('websites', $params['websites']);
        }

        if(isset($params['exclude']['parent_exclude']) && isset($params['exclude']['parent_exclude']['navigations']) && !empty($params['exclude']['parent_exclude']['navigations'])){
            $query->andWhere($query->expr()->notIn('p.id',':exclude_ids'))
                ->setParameter('exclude_ids',$params['exclude']['parent_exclude']['navigations']);
        }
        return $query;
    }


} 