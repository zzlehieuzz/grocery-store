<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class ItemMasterConst extends BaseEntity
{
    //item_detailed: アイテム詳細
    const ITEM_DETAILED_STRENGTHENING_EXP       = 1;//取得EXP
    const ITEM_DETAILED_STRENGTHENING_RARITY    = 2;//対応レアリティ

    const ITEM_DETAILED_RECOVERY_ACTION     = 1;//行動力
    const ITEM_DETAILED_RECOVERY_DUEL       = 2;//デュエルポイント
    const ITEM_DETAILED_RECOVERY_MEMORY     = 3;//メモリー

    const ITEM_DETAILED_CARD_HP     = 1;//HP
    const ITEM_DETAILED_CARD_STM    = 2;//STM

    const ITEM_GOLD_KEY_ID = 11601 ;//アイテム金鍵のID
    const ITEM_SILVER_KEY_ID = 11602 ;//アイテム金鍵のID
    const ITEM_EVOLUTION = 10501 ;//アイテム金鍵のID
}
