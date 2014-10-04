<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class GoodsInformationConst extends BaseEntity
{
    const INSTANT_ITEM_RECOVERY_POWER   = 1;//行動力回復
    const INSTANT_ITEM_DUEL_POINT       = 2;//デュエルポイント回復
    const INSTANT_ITEM_MEMORY_POINT     = 3;//メモリー回復
    const INSTANT_ITEM_CHIP             = 4;//チップ
    const INSTANT_ITEM_ERUFE            = 5;//エルフェ
    const INSTANT_ITEM_GACHA_POINT      = 6;//ガチャポイント
    const INSTANT_ITEM_RUNE             = 7;//ルーン
    const INSTANT_ITEM_EXP              = 8;//ユーザー経験値
}
