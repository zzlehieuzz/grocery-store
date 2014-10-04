<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class UserFriendInformationConst extends BaseEntity
{
    //state: 状態
    const STATE_FRIEND           = 1;   //フレンド
    const STATE_PENDING          = 2;   //申請中
    const STATE_WAITING_APPROVAL = 3;   //承認待ち
}
