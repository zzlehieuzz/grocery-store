<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class UserRightsInformationConst extends BaseEntity
{
    // classification:種別
    const CLASSIFICATION_TAP_I              = 1;//タップi
    const CLASSIFICATION_FREE_GACHA         = 2;//無料ガチャ
    const CLASSIFICATION_FREE_GACHA_TIME    = 3;//無料ガチャ時間別
    const CLASSIFICATION_FREE_GACHA_DX      = 4;//無料ガチャDX
    const CLASSIFICATION_GREETING           = 5;//挨拶
    const CLASSIFICATION_BATTLE             = 6;//バトル
    const CLASSIFICATION_HOT_SPRINGS        = 7;//温泉
    const CLASSIFICATION_LAB                = 8;//ラボ
    const CLASSIFICATION_ERRAND             = 9;//お使い

    // target:対象
    const TARGET_0              = 0;
    const TARGET_1              = 1;
    const TARGET_2              = 2;
    const TARGET_MORNING_TIME   = 10;//モーニングタイム
    const TARGET_LUNCH_TIME     = 20;//ランチタイム
    const TARGET_DINER_TIME     = 30;//ディナータイム
    const TARGET_MIDNIGHT       = 40;//ミッドナイト
}
