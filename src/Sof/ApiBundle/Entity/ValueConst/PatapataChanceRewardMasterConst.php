<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class PatapataChanceRewardMasterConst extends BaseEntity
{
    const CARD_TYPE_CARD     = 1; //カード
    const CARD_TYPE_ITEM     = 2; //ITEM
    const CARD_TYPE_PIECE    = 3; //ピース
    const CARD_TYPE_RECOVERY = 4; //ステ回復
    const CARD_TYPE_ERUFE    = 5; //エルフェ
    const CARD_TYPE_PLAN     = 6; //素材
    const CARD_TYPE_WILD     = 97; //ワイルド
    const CARD_TYPE_WOPEN    = 98; //Wオープン
    const CARD_TYPE_RARE     = 99; //レア
    const CARD_TYPE_FAIL     = 100; //失敗
}
