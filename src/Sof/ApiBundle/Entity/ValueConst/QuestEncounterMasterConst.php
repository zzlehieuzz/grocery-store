<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class QuestEncounterMasterConst extends BaseEntity
{
    //e_stock: Eストック
    const E_STOCK_ORIGINAL_STOCK_SYSTEM = 0;//元ストック系
    const E_STOCK_BATTLE_SPIRITS        = 1;//精霊バトル
    const E_STOCK_RAID_BOSS             = 3;//レイドボス
}
