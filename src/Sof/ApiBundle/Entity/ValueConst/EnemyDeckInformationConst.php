<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class EnemyDeckInformationConst extends BaseEntity
{
    // deck_attribute: デッキ属性
    const DECK_ATTRIBUTE_SPIRIT    = 1;  // 精霊バトル
    const DECK_ATTRIBUTE_AREA_BOSS = 2;  // エリアボス
    const DECK_ATTRIBUTE_RAID_BOSS = 3;  // レイドボス
}
