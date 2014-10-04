<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class QuestEventInformationConst extends BaseEntity
{
    //event_id: イベントID
    const EVENT_ID_ZERO            = 0;
    const EVENT_ID_NONE_FOUND      = 1;//発見なし
    const EVENT_ID_RUNE            = 2;//ルーン
    const EVENT_ID_PATAPATA        = 3;//ぱたぱた
    const EVENT_ID_PIECE           = 4;//ピース
    const EVENT_ID_GOLDEN_BOX      = 5;//金箱
    const EVENT_ID_SILVER_BOX      = 6;//銀箱
    const EVENT_ID_CUBE            = 7;//cube
    const EVENT_ID_EXP_CARD_LEADER = 8;//リーダーカードの経験値
    const EVENT_ID_CHICKEN         = 9;//ひよこ・にわとり
    const EVENT_ID_MEET_OTHER_USER = 10;//他のユーザーと会う
    const EVENT_ID_FEVER           = 11;//フィーバー
    const EVENT_ID_MEET_GHOST      = 21;//精霊と遭遇
    const EVENT_ID_MEET_RAID_BOSS  = 22;//レイドボスと遭遇
}
