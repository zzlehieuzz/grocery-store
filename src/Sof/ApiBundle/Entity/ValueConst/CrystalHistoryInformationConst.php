<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class CrystalHistoryInformationConst extends BaseEntity
{
    //section:区分
    const SECTION_1 = 1;//ユーザー購入時
    const SECTION_2 = 2;//クリスタル配布
    const SECTION_3 = 3;

    //balance:
    const BALANCE_0 = 0;

    //useCount:利用数
    const USE_COUNT_0 = 0;

    //use:用途
    const USE_CRYSTAL_DISTRIBUTION = "配布されたクリスタル";
    const USE_CRYSTAL_PURCHASE     = "購入したクリスタル";
}
