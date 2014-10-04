<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class PostReceiptHistoryInformationConst extends BaseEntity
{
    // state:状態
    const STATE_UNOPENED                = 0; //未開封
    const STATE_OPEN                    = 1; //開封
    const STATE_DELETE_USER_VIEWPOINT   = 2; //ユーザー視点から削除
}
