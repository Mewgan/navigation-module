<?php

namespace Jet\Modules\Navigation\Models;

use Doctrine\Common\Collections\ArrayCollection;
use JetFire\Db\Model;

/**
 * Class NavigationItem
 * @package Jet\Models
 * @Entity
 * @Table(name="navigations_items")
 */
class NavigationItem extends Model
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * @Column(type="string")
     */
    protected $title;
    /**
     * @ManyToOne(targetEntity="Navigation", inversedBy="items", onDelete="CASCADE")
     * @JoinColumn(name="navigation_id", referencedColumnName="id")
     */
    protected $navigation;
    /**
     * @OneToMany(targetEntity="Navigation", mappedBy="parent", onDelete="SET NULL")
     */
    protected $childrens;
    /**
     * @ManyToOne(targetEntity="Navigation", inversedBy="childrens", cascade={"persist", "remove"})
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;
    /**
     * @Column(type="string")
     */
    protected $route;
    /**
     * @Column(type="array", nullable=true)
     */
    protected $options;

    /**
     * Navigation Item constructor.
     */
    public function __construct() {
        $this->childrens = new ArrayCollection();
    }


}