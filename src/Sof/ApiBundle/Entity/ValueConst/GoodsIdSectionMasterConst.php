<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class GoodsIdSectionMasterConst extends BaseEntity
{
    const GOODS_ID_SECTION_ITEM             = 1;//アイテム
    const GOODS_ID_SECTION_GOODS            = 2;//商品
    const GOODS_ID_SECTION_EVENT_PRODUCT    = 3;//イベント商品
    const GOODS_ID_SECTION_ENEMY_PT         = 4;//敵PT
    const GOODS_ID_SECTION_EVENT_ENEMY_PT   = 5;//イベント敵PT
    const GOODS_ID_SECTION_CARD             = 6;//カード
    const GOODS_ID_SECTION_ENEMY_CARD       = 7;//敵カード
    const GOODS_ID_SECTION_CRYSTAL          = 8;//クリスタル
    const GOODS_ID_SECTION_RECOVERY         = 9;//ユーザーステータス回復
    const GOODS_ID_SECTION_EQUIPMENT        = 10;//装備
    const GOODS_ID_FABRICATION_DRAWING      = 11;//製作図
}
