<?php

namespace Jet\Modules\Navigation\Models;

use Doctrine\Common\Collections\ArrayCollection;
use JetFire\Db\Model;
use Doctrine\ORM\Mapping;

/**
 * Class Navigation
 * @package Jet\Models
 * @Entity
 * @Table(name="navigations")
 * @HasLifecycleCallbacks
 */
class Navigation extends Model implements \JsonSerializable
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
    protected $name;
    /**
     * @OneToMany(targetEntity="NavigationItem", mappedBy="navigation")
     */
    protected $items;
    /**
     * @ManyToOne(targetEntity="Jet\Models\Website")
     * @JoinColumn(name="website_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $website;
    /**
     * @Column(type="datetime")
     */
    protected $created_at;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * Navigation constructor.
     */
    public function __construct() {
        $this->items = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt(\DateTime $updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @PrePersist
     */
    public function onPrePersist(){
        $this->setCreatedAt(new \DateTime('now'));
        $this->setUpdatedAt(new \DateTime('now'));
    }
    /**
     * @PreUpdate
     */
    public function onPreUpdate(){
        $this->setUpdatedAt(new \DateTime('now'));
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
            'name' => $this->getName(),
            'items' => $this->getItems(),
            'website' => [
                'id' => $this->getWebsite()->getId(),
                'domain' => $this->getWebsite()->getDomain(),
            ],
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}