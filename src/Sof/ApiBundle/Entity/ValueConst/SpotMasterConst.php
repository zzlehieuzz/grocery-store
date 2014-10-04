<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class SpotMasterConst extends BaseEntity
{
    //hold_classification: 開催種別
    const HOLD_CLASSIFICATION_PERMANENT        = 0;//常設
    const HOLD_CLASSIFICATION_JUDGMENT_DAY     = 1;//曜日判定
    const HOLD_CLASSIFICATION_PERIOD_SPECIFIED = 2;//開催期間指定
    const HOLD_CLASSIFICATION_HOLD_TIME_ZONE   = 3;//開催時間帯指定

    //hold_day_of_the_week: 開催曜日 -> Base:week

    //advance: 進行
    const ADVANCE_SPOT_MOVEMENT = -1;//常設

    //Default id in db
    const FIRST_SPOT_ID  = 10100;
    const SECOND_SPOT_ID = 10200;

    //score_classification : スコア種別
    const SCORE_CLASSIFICATION_NORMAL_RUNE       = 0;//通常ルーン
    const SCORE_CLASSIFICATION_MORE_EVENT_SCORE  = 1;//以上～イベント用スコア
}
