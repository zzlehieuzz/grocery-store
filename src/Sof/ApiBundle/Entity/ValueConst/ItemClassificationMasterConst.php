<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class ItemClassificationMasterConst extends BaseEntity
{
    //item_classification: アイテム種別
    const ITEM_CLASSIFICATION_RECOVERY                    = 1;//回復アイテム
    const ITEM_CLASSIFICATION_CARD_RECOVERY               = 2;//レイシの雫
    const ITEM_CLASSIFICATION_BATTLE_RECOVERY             = 3;//戦闘中回復アイテム
    const ITEM_CLASSIFICATION_HOURS_SHORTER               = 4;//時間短縮アイテム
    const ITEM_CLASSIFICATION_STRENGTHENING               = 5;//強化アイテム
    const ITEM_CLASSIFICATION_EVOLUTION                   = 6;//進化アイテム
    const ITEM_CLASSIFICATION_REVENGE                     = 7;//リベンジ
    const ITEM_CLASSIFICATION_TRAP                        = 8;//トラップ
    const ITEM_CLASSIFICATION_WEAPON_REPAIR               = 9;//砥石
    const ITEM_CLASSIFICATION_TREASURE_CHEST              = 10;//宝箱
    const ITEM_CLASSIFICATION_EGGS                        = 11;//卵
    const ITEM_CLASSIFICATION_RECIPE                      = 12;//製作図
    const ITEM_CLASSIFICATION_CRYSTAL                     = 13;//クリスタル
    const ITEM_CLASSIFICATION_EXTENSION_TICKET            = 14;//期間延長チケット
    const ITEM_CLASSIFICATION_GACHA_TICKET                = 15;//ガチャチケット
    const ITEM_CLASSIFICATION_KEY                         = 16;//鍵
    const ITEM_CLASSIFICATION_TURNING                     = 17;//めくり
    const ITEM_CLASSIFICATION_HOURS_SHORTENING            = 21;//時間短縮アイテム（お使い）
    const ITEM_CLASSIFICATION_HOURS_SHORTER_LAB           = 22;//時間短縮アイテム（ラボ）
    const ITEM_CLASSIFICATION_EXTENSION_TICKET_VAULT      = 31;//期間延長チケット（保管庫）
    const ITEM_CLASSIFICATION_EXTENSION_TICKET_ARMORY     = 32;//期間延長チケット（武器庫）
    const ITEM_CLASSIFICATION_EXTENSION_TICKET_YOUR       = 33;//期間延長チケット（お使い）
    const ITEM_CLASSIFICATION_EXTENSION_TICKET_HOT_SPRING = 34;//期間延長チケット（温泉）
    const ITEM_CLASSIFICATION_EXTENSION_TICKET_LAB        = 35;//期間延長チケット（ラボ）
    const ITEM_CLASSIFICATION_IMMEDIATE_RESOLUTION        = 100;//即時解決
    const ITEM_CLASSIFICATION_EQUIPMENT                   = 201;//装備
    const ITEM_CLASSIFICATION_ACCESSORIES                 = 202;//アクセサリ
}
