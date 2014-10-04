<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class ShopMasterConst extends BaseEntity
{
    //business_classification:営業種別
    const BUSINESS_CLASSIFICATION_PERMANENT        = 0;//常設
    const BUSINESS_CLASSIFICATION_JUDGMENT_DAY     = 1;//曜日判定
    const BUSINESS_CLASSIFICATION_PERIOD_SPECIFIED = 2;//営業期間指定
    const BUSINESS_CLASSIFICATION_HOURS_SPECIFIED  = 3;//営業時間帯指定

    //business_week: 営業曜日 -> Base:week
}
