<?php

namespace Jet\Modules\Navigation\Models;

use Doctrine\Common\Collections\ArrayCollection;
use JetFire\Db\Model;

/**
 * Class NavigationItem
 * @package Jet\Models
 * @Entity
 * @Table(name="navigation_items")
 */
class NavigationItem extends Model implements \JsonSerializable
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
     * @ManyToOne(targetEntity="Navigation", inversedBy="items")
     * @JoinColumn(name="navigation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $navigation;
    /**
     * @OneToMany(targetEntity="Navigation", mappedBy="parent")
     */
    protected $childrens;
    /**
     * @ManyToOne(targetEntity="Navigation", inversedBy="childrens")
     * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $parent;
    /**
     * @Column(type="string")
     */
    protected $url;
    /**
     * @Column(type="string")
     */
    protected $type = 'custom';
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $type_id;
    /**
     * @Column(type="integer")
     */
    protected $position = 0;

    /**
     * Navigation Item constructor.
     */
    public function __construct() {
        $this->childrens = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * @param Navigation $navigation
     */
    public function setNavigation(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    /**
     * @return mixed
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @param mixed $childrens
     */
    public function setChildrens($childrens)
    {
        $this->childrens = $childrens;
    }

    /**
     * @return NavigationItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param NavigationItem $parent
     */
    public function setParent(NavigationItem $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * @param mixed $type_id
     */
    public function setTypeId($type_id)
    {
        $this->type_id = $type_id;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'url' => $this->getUrl(),
            'type' => $this->getType(),
            'type_id' => $this->getTypeId(),
            'position' => $this->getPosition(),
            'childrens' => $this->getChildrens(),
            'parent' => $this->getParent(),
            'navigation' => $this->getNavigation()
        ];
    }
}