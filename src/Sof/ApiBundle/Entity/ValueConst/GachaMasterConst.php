<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class GachaMasterConst extends BaseEntity
{
    //business_classification: 営業種別
    const BUSINESS_CLASSIFICATION_PERMANENT                  = 0;//常設
    const BUSINESS_CLASSIFICATION_JUDGMENT_DAY               = 1;//曜日判定
    const BUSINESS_CLASSIFICATION_OPERATING_PERIOD_SPECIFIED = 2;//営業期間指定
    const BUSINESS_CLASSIFICATION_BUSINESS_HOURS_SPECIFIED   = 3;//営業時間帯指定

    //gacha_type: ガチャ種別
    const GACHA_TYPE_OTHER      = 1;//その他
    const GACHA_TYPE_FREE_GACHA = 2;//無料ガチャ

    //gacha_process_type: ガチャ処理種別
    const GACHA_PROCESS_TYPE_NORMAL  = 1;//通常
    const GACHA_PROCESS_TYPE_BOX     = 2;//BOX
    const GACHA_PROCESS_TYPE_STEP_UP = 3;//ステップアップ
}
