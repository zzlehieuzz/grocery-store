<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class EquipmentProprietyDecisionMasterConst extends BaseEntity
{
    //equipment_type: 装備タイプ
    const EQUIPMENT_TYPE_ACCESSORIES = 1;//アクセサリ
    const EQUIPMENT_TYPE_SWORD       = 2;//ソード
    const EQUIPMENT_TYPE_HAMMER      = 3;//ハンマー
    const EQUIPMENT_TYPE_LANCE       = 4;//ランス
    const EQUIPMENT_TYPE_SHIELD      = 5;//シールド
    const EQUIPMENT_TYPE_RIFLE       = 6;//ライフル
    const EQUIPMENT_TYPE_BOW         = 7;//ボウ
    const EQUIPMENT_TYPE_WAND        = 8;//ワンド
    const EQUIPMENT_TYPE_KNIFE       = 9;//ナイフ

    //job_id: JOBID
    const JOB_ID_WAR = 1;//WAR(戦)
    const JOB_ID_PLD = 2;//PLD(ナ)
    const JOB_ID_DRG = 3;//DRG(竜)
    const JOB_ID_SHA = 4;//SHA(術)
    const JOB_ID_MAG = 5;//MAG(魔)
    const JOB_ID_ARC = 6;//ARC(射)
    const JOB_ID_SNP = 7;//SNP(弾)
    const JOB_ID_BSP = 8;//BSP(聖)
    const JOB_ID_SNG = 9;//SNG(歌)
    const JOB_ID_BRD = 10;//BRD(詩)
}
