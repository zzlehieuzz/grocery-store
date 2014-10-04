<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class UserAssetInformationConst extends BaseEntity
{
    //money_classification:貨幣種別
    const MONEY_CLASSIFICATION_CRYSTAL          = 0;//クリスタル、
    const MONEY_CLASSIFICATION_CHIPPU           = 1;//チップ、
    const MONEY_CLASSIFICATION_ERUFE            = 2;//エルフェ、
    const MONEY_CLASSIFICATION_GACHA_POINT      = 3;//ガチャポイント
    const MONEY_CLASSIFICATION_DMM_PST          = 4;//DMMpts
}
