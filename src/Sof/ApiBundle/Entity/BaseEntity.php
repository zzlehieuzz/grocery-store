<?php

namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

abstract class BaseEntity
{
    //flag: 有|無
    const FLAG_ON   = 1;//有
    const FLAG_OFF  = 0;//無

    //week: 曜日
    const WEEK_SUN = 0;//日
    const WEEK_MON = 1;//月
    const WEEK_TUE = 2;//火
    const WEEK_WED = 3;//水
    const WEEK_THU = 4;//木
    const WEEK_FRI = 5;//金
    const WEEK_SAT = 6;//土

    const KONMA    = ',';
    const DIVISION = '/';

    const ID_ZERO   = 0;

    const SENDER_MANAGEMENT    = '運営';
    const SENDER_NORMAL    = '送付主';

    //shopId: ショップID
    const SHOP_ID_DISTRIBUTION = 99; //ログインボーナス用配布ショップID

    //shopId: ショップID
    const SHOP_ID_EVENT_REWARD = 98; //イベント報酬用のショップID

    const CARD_WILD_NO_1 = 97;

    const CARD_WILD_NO_2 = 98;

    //classification: リスト種別
    const CLASSIFICATION_AREA_BOSS        = 1;
    const CLASSIFICATION_RAID_BOSS        = 2;
    const CLASSIFICATION_RESCUE_RAID_BOSS = 3;

    const NEGATIVE_ONE = -1;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Assert\Type(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
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
