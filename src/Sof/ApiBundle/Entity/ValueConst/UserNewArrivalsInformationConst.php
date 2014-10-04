<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class UserNewArrivalsInformationConst extends BaseEntity
{

    // managementId:管理ID
    const MANAGEMENT_FREE_GACHA               = 1;//無料ガチャ
    const MANAGEMENT_PEACE_DUEL               = 2;//ピースデュエル
    const MANAGEMENT_POST                     = 3;//ポスト
    const MANAGEMENT_MAINTENANCE_INFORMATION  = 4;//メンテナンス情報
    const MANAGEMENT_TAP_I                    = 5;//タップｉ
    const MANAGEMENT_LAB                      = 6;//ラボ
    const MANAGEMENT_HOT_SPRINGS              = 7;//温泉
    const MANAGEMENT_FRIEND_COMMENT           = 8;//フレンドコメント
    const MANAGEMENT_FRIEND_REQUEST           = 9;//フレンド申請
    const MANAGEMENT_ERRAND                   = 10;//お使い

    //display_flag: 表示フラグ
    const DISPLAY_FLAG_NƠNE = 0;//非表示データ
    const DISPLAY_FLAG_YES  = 1;//表示データ
}
