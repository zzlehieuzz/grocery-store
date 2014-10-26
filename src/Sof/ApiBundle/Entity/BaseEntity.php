<?php

namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

abstract class BaseEntity
{
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Assert\Type(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Assert\Type(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @Assert\Type(type="datetime")
     */
    protected $deletedAt;

    /**
     * @ORM\Column(name="created_by", type="integer", nullable=false)
     * @Assert\Type(type="integer")
     */
    protected $createdBy;

    /**
     * @ORM\Column(name="updated_by", type="integer", nullable=false)
     * @Assert\Type(type="integer")
     */
    protected $updatedBy;

    /**
     * @ORM\Column(name="deleted_by", type="integer", nullable=true)
     * @Assert\Type(type="integer")
     */
    protected $deletedBy;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
      $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
      return $this->createdAt;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
      $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
      return $this->createdBy;
    }

    /**
     * @param mixed $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
      $this->deletedAt = $deletedAt;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
      return $this->deletedAt;
    }

    /**
     * @param mixed $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
      $this->deletedBy = $deletedBy;
    }

    /**
     * @return mixed
     */
    public function getDeletedBy()
    {
      return $this->deletedBy;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
      $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
      return $this->updatedAt;
    }

    /**
     * @param mixed $updatedBy
     */
    public function setUpdatedBy($updatedBy)
    {
      $this->updatedBy = $updatedBy;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
      return $this->updatedBy;
    }
}
