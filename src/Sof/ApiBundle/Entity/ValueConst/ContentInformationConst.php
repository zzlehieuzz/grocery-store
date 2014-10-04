<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class ContentInformationConst extends BaseEntity
{
    //0：デフォルト、１：メインストーリー用演出、２：イベント用演出
    const FLG_DEFAULT = 0;
    const FLG_MAIN    = 1;
    const FLG_EVENT   = 2;
}
