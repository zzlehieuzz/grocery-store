<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Lib\DateUtil;

class C00CommonApiController extends BaseController
{
    public function preAction()
    {
        $this->getApiParams();
    }

    /**
     * @Route("/C00_0208_Test", name="C00_0208_Test")
     */
    public function C00_0208_TestAction()
    {
        $a = array(
            'name' => 'hieu',
        );

        return $this->redirect($this->generateUrl('S101_4', $a));

//        return $this->apiResponse(array());
    }

    /**
     * @Route("/C00_0208_UserVolumeInformationUpdate", name="C00_0208_UserVolumeInformationUpdate")
     */
    public function C00_0208_UserVolumeInformationUpdateAction()
    {
        $commonService = $this->getCommonService();
        $params = $this->getApiInput(
            array('INT' => array('gameVolumeSe', 'gameVolumeBgm', 'gameVolumeVoice', 'mute'))
        );

        //ユーザー音量設定情報更新
        //共通関数「ユーザー音量情報更新処理」を実行する
        //処理結果 = ユーザー音量情報更新処理(ゲーム音量SE, ゲーム音量BGM, ゲーム音量音声, ミュート)
        $response = $commonService->C00_9982_UserVolumeInformationUpdateProcess(
            $params['userId'],
            $params['gameVolumeSe'],
            $params['gameVolumeBgm'],
            $params['gameVolumeVoice'],
            $params['mute']
        );

        return $this->apiResponse($response);
    }

    /**
     * @Route("/C00_0209_FooterInformation", name="C00_0209_FooterInformation")
     */
    public function C00_0209_FooterInformationAction()
    {
        $commonService = $this->getCommonService();

        //(共通処理参照）
        //処理結果別のチェック処理
        //020999その他のエラー
        //
        //フッター情報取得処理
        //共通関数「フッター情報取得処理」を実行する
        //フッター情報リスト = フッター情報取得処理(void)
        $response = $commonService->C00_9906_FooterInformation();
        return $this->apiResponse($response);
    }

    /**
     * @Route("/C26_0246_FlashNoticeInformationList", name="C26_0246_FlashNoticeInformationList")
     */
    public function C26_0246_FlashNoticeInformationListAction()
    {
        $timeNow = DateUtil::getTimeNow();

        //Flash告知情報取得
        //Flash告知リスト = Flash告知マスタ(優先度, コンテンツ)
        //条件、期間開始 <= 現在日時 < 期間終了
        //並び順、優先度
        $response = $this->getEntityService()->getAllData(
            'FlashNoticeMaster',
            array(
                'conditions' => array(
                    'periodStart' => array('<=' => $timeNow),
                    'periodEnd' => array('>' => $timeNow)
                ),
                'selects' => array('priority','content'),
                'orderBy' => array('priority')
            )
        );

        return $this->apiResponse($response);
    }

    /**
     * @Route("/C26_0245_GetMasterCodeData", name="C26_0245_GetMasterCodeData")
     */
    public function C26_0245_GetMasterCodeDataAction()
    {
        //装備可否判定マスタ取得
        //装備可否判定マスタリスト = 装備可否判定マスタ（装備タイプ, JOBID）
        //条件なし　（全件取得）
        $responseList['equipmentPropertyDecisionMasterList'] = $this->getEntityService()->getAllData('EquipmentProprietyDecisionMaster',
            array('selects' => array('equipmentType','jobId'))
        );

        //コードマスタ取得
        //コードマスタリスト = コードマスタ（ID種別, 管理ID, 文言）
        //条件なし　（全件取得）
        $responseList['codeMasterList'] = $this->getEntityService()->getAllData('CodeMaster',
            array('selects' => array('idClassification','managementId','wording'))
        );

        //スキルマスタ取得
        //スキルマスタリスト = スキルマスタ（スキルID, スキル名称, スキル説明, コンテンツ, SE）
        //条件なし　（全件取得）
        //SF_TODO:hamd QA スキルマスタ table hasn't container スキル説明, コンテンツ, SE fileds
        $responseList['skillMasterList'] = $this->getEntityService()->getAllData('BtSkillMaster',
            array('selects' => array('skillId','skillName'))
        );

        return $this->apiResponse($responseList);
    }
}