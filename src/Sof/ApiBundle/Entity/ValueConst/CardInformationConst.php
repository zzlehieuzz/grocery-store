<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class CardInformationConst extends BaseEntity
{
    //state: 状態
    const STATE_SEIZON  = 1;//生存
    const STATE_SUIJAKU = 2;//衰弱

    //state2: 状態２
    const STATE_2_TAIKICHU    = 1;//待機中
    const STATE_2_OTSUKAICHU  = 2;//お使い中
    const STATE_2_ONSEN       = 3;//温泉中

    //job_type: ジョブタイプ
    const JOB_TYPE_ATTACKER   = 1;//アタッカー
    const JOB_TYPE_LONG_RANGE = 2;//ロングレンジ
    const JOB_TYPE_HEALER     = 3;//ヒーラー

    const ULTIMATE_GAUGE_100  = 100;//アルティメットゲージ

    const DECK_EDITING       = 1;//デッキ編集
    const CARD_STRENGTHENING = 2;//カード強化
    const EVOLUTION_CARD     = 3;//カード進化
    const HOT_SPRINGS        = 4;//温泉
    const ERRAND             = 5;//お使い
    const CARD_LIST_MY_ROOM  = 6;//カード一覧（マイルーム）
}
