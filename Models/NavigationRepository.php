<?php

namespace Jet\Modules\Navigation\Models;

use Doctrine\ORM\EntityRepository;

/**
 * Class NavigationRepository
 * @package Jet\Modules\Navigation\Models
 */
class NavigationRepository extends EntityRepository
{

    /**
     * @param $websites
     * @return array
     */
    public function listAll($websites)
    {

        $query = Navigation::queryBuilder();

        $query->select('partial n.{id,name}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Navigation\Models\Navigation', 'n')
            ->leftJoin('n.website', 'w');

        $query->where($query->expr()->in('w.id',':websites'))
                ->setParameter('websites',$websites);

        if(isset($exclude['parent_exclude']) && isset($exclude['parent_exclude']['navigations']) && !empty($exclude['parent_exclude']['navigations'])){
            $query->andWhere($query->expr()->notIn('p.id',':exclude_ids'))
                ->setParameter('exclude_ids',$exclude['parent_exclude']['navigations']);
        }

        return $query->getQuery()->getArrayResult();
    }

} 