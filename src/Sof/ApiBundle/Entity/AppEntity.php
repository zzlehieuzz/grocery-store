<?php

namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Gedmo\Mapping\Annotation as Gedmo;

abstract class AppEntity
{
  /**
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   */
  protected $createdAt;

  /**
   * @ORM\Column(name="updated_at", type="datetime", nullable=false)
   */
  protected $updatedAt;

  /**
   * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
   */
  protected $deletedAt;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
   */
  protected $createdBy;

  /**
   * @ORM\ManyToOne(targetEntity="Section")
   * @ORM\JoinColumn(name="created_section_id", referencedColumnName="id", nullable=true)
   */
  protected $createdSection;

  /**
   * @ORM\ManyToOne(targetEntity="Staff")
   * @ORM\JoinColumn(name="created_staff_id", referencedColumnName="id", nullable=true)
   */
  protected $createdStaff;

  /**
   * @ORM\Column(name="created_display_name", type="string", length=50, nullable=true)
   */
  protected $createdDisplayName;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
   */
  protected $updatedBy;

  /**
   * @ORM\ManyToOne(targetEntity="Section")
   * @ORM\JoinColumn(name="updated_section_id", referencedColumnName="id", nullable=true)
   */
  protected $updatedSection;

  /**
   * @ORM\ManyToOne(targetEntity="Staff")
   * @ORM\JoinColumn(name="updated_staff_id", referencedColumnName="id", nullable=true)
   */
  protected $updatedStaff;

  /**
   * @ORM\Column(name="updated_display_name", type="string", length=50, nullable=true)
   */
  protected $updatedDisplayName;

  /**
   * Returns createdAt.
   *
   * @return DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Returns updatedAt.
   *
   * @return DateTime
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  /**
   * Returns deletedAt.
   *
   * @return DateTime
   */
  public function getDeletedAt()
  {
    return $this->deletedAt;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setDeletedAt($deletedAt)
  {
    $this->deletedAt = $deletedAt;

    return $this;
  }

  /**
   * Returns createdBy.
   *
   * @return int
   */
  public function getCreatedBy()
  {
    return $this->createdBy;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setCreatedBy($createdBy)
  {
    $this->createdBy = $createdBy;

    return $this;
  }

  /**
   * Returns createdSection.
   *
   * @return int
   */
  public function getCreatedSection()
  {
    return $this->createdSection;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setCreatedSection($createdSection)
  {
    $this->createdSection = $createdSection;

    return $this;
  }

  /**
   * Returns createdStaff
   *
   * @return int
   */
  public function getCreatedStaff()
  {
    return $this->createdStaff;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setCreatedStaff($createdStaff)
  {
    $this->createdStaff = $createdStaff;

    return $this;
  }

  /**
   * Returns createdDisplayName
   *
   * @return string
   */
  public function getCreatedDisplayName()
  {
    return $this->createdDisplayName;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setCreatedDisplayName($createdDisplayName)
  {
    $this->createdDisplayName = $createdDisplayName;

    return $this;
  }

  /**
   * Returns updatedBy.
   *
   * @return int
   */
  public function getUpdatedBy()
  {
    return $this->updatedBy;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setUpdatedBy($updatedBy)
  {
    $this->updatedBy = $updatedBy;

    return $this;
  }

  /**
   * Returns updatedSection.
   *
   * @return int
   */
  public function getUpdatedSection()
  {
    return $this->updatedSection;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setUpdatedSection($updatedSection)
  {
    $this->updatedSection = $updatedSection;

    return $this;
  }

  /**
   * Returns updatedStaff
   *
   * @return int
   */
  public function getUpdatedStaff()
  {
    return $this->updatedStaff;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setUpdatedStaff($updatedStaff)
  {
    $this->updatedStaff = $updatedStaff;

    return $this;
  }

  /**
   * Returns updatedDisplayName
   *
   * @return string
   */
  public function getUpdatedDisplayName()
  {
    return $this->updatedDisplayName;
  }

  /**
   * Returns Entity.
   *
   * @return Entity
   */
  public function setUpdatedDisplayName($updatedDisplayName)
  {
    $this->updatedDisplayName = $updatedDisplayName;

    return $this;
  }

  public function getClassName()
  {
    return get_class($this);
  }
}
