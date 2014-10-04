<?php

namespace Sof\ApiBundle\Service\CommonFunc;

use Proxies\__CG__\Sof\ApiBundle\Entity\HelpMaster;
use Sof\ApiBundle\Entity\BaseEntity;
use Sof\ApiBundle\Entity\BaseRepository;
use Sof\ApiBundle\Entity\DmmUserInformation;
use Sof\ApiBundle\Entity\ManagementNoticeInformation;
use Sof\ApiBundle\Entity\ValueConst\DmmUserInformationConst;
use Sof\ApiBundle\Entity\ValueConst\HelpMasterConst;
use Sof\ApiBundle\Entity\ValueConst\UserInformationConst;
use Sof\ApiBundle\Lib\DateUtil;

trait C00CommonApiFunc
{
    use BaseFunc;

    // API SF設計書20131227-インターフェース仕様書_共通系
    public function C00_9904_UserVolumeInformation($userId)
    {
        //ユーザー音量情報リスト = ユーザー音量情報(ゲーム音量SE, ゲーム音量BGM, ゲーム音量音声)
        // 条件、ユーザーID = ユーザーID
        $userVolumeInformation = $this->getEntityService()->getFirstData(
            'UserVolumeInformation',
            array(
                'conditions' => array(
                    'userId' => $userId
                ),
                'selects' => array('gameVolumeSe', 'gameVolumeBgm', 'gameVolumeVoice')
            )
        );

        return $userVolumeInformation;
    }

    public function C00_9905_ScreenParts($screenId)
    {
        //コンテンツ情報リスト = コンテンツ情報（画面ID, 管理ID, コンテンツ情報）
        // 条件、画面ID = 画面ID AND 期間開始 ≦ 現在日時 < 期間終了
        $now = DateUtil::getTimeNow();
        $contentInformationList = $this->getEntityService()->getAllData(
            'ContentInformation',
            array(
                'conditions' => array(
                    'screenId' => $screenId,
                    'periodStart' => array("<=" => $now),
                    'periodEnd' => array(">" => $now)
                ),
                'selects' => array('screenId', 'managementId', 'contentInformation')
            )
        );

        return $contentInformationList;
    }

    public function C00_9906_FooterInformation()
    {
        $now = DateUtil::getTimeNow();

        //コンテンツ情報リスト = コンテンツ情報（画面ID, 管理ID, コンテンツ情報）
        // 条件、画面ID = 画面ID AND 期間開始 ≦ 現在日時 < 期間終了
        $bannerInformationList = $this->getEntityService()->getAllData(
            'BannerInformation',
            array(
                'conditions' => array(
                    'periodStart' => array('<=' => $now),
                    'periodEnd' => array('>' => $now)
                ),
                'selects' => array('bannerInformationValue', 'content')
            )
        );

        //運営からのお知らせ情報取得
        //「運営告知情報取得」関数の実行
        //メンテ告知リスト取得
        //運営告知情報リスト取得
        $managementNoticeList = $this->C00_9907_ManagementNoticeInformation();

        //ヘルプ情報取得
        //ヘルプ情報リスト = ヘルプマスタ（章立て, 内容）
        //条件、内容区分 = 0
        $helpMasterList = $this->getEntityService()->getAllData(
            'HelpMaster',
            array(
                'conditions' => array(
                    'contentSection' => HelpMasterConst::CONTENT_SECTION
                ),
                'selects' => array('chapters', 'content')
            )
        );

        //フッター情報リスト作成
        //フッター情報リスト = バナー情報リスト
        //フッター情報リスト += メンテ告知リスト
        //フッター情報リスト += 運営告知情報リスト
        //フッター情報リスト += ヘルプ情報リスト
        $footerInformationList['bannerList'] = $bannerInformationList;
        $footerInformationList['operationNoticeList'] = $managementNoticeList['operationNoticeList'];
//        $footerInformationList['maintenanceNoticeList'] = $managementNoticeList['maintenanceNoticeList'];
        $footerInformationList['helpMasterList'] = $helpMasterList;

        return $footerInformationList;
    }

    public function C00_9907_ManagementNoticeInformation()
    {
        $now = DateUtil::getTimeNow();

        //メンテ告知リスト取得
        //メンテ告知リスト = 運営告知情報(告知日時, 告知内容)
        //条件、告知日時 ≦ 現在日時 < 有効期限 AND 告知区分 = 1 AND 表示フラグ = 1
        $maintenanceNoticeList = $this->getEntityService()->getAllData(
            'ManagementNoticeInformation',
            array(
                'conditions' => array(
                    'noticeDate' => array("<=" => $now),
                    'expirationDate' => array(">" => $now),
                    'displayFlag' => ManagementNoticeInformation::FLAG_ON,
                    'noticeSection' => ManagementNoticeInformation::NOTICE_SECTION_MAINTENANCE
                ),
                'selects' => array('noticeDate', 'noticeContent')
            )
        );

        //運営告知情報取得
        //メンテ告知リスト = 運営告知情報(告知日時, 告知内容)
        //条件、告知日時 ≦ 現在日時 < 有効期限 AND 告知区分 = 2 AND 表示フラグ = 1
        $operationNoticeList = $this->getEntityService()->getAllData(
            'ManagementNoticeInformation',
            array(
                'conditions' => array(
                    'noticeDate' => array("<=" => $now),
                    'expirationDate' => array(">" => $now),
                    'displayFlag' => ManagementNoticeInformation::FLAG_ON,
                    'noticeSection' => ManagementNoticeInformation::NOTICE_SECTION_OPERATION
                ),
                'selects' => array('noticeDate', 'noticeContent')
            )
        );

        $managementNoticeInformationList = array(
            'operationNoticeList' => $operationNoticeList,
            'maintenanceNoticeList' => $maintenanceNoticeList
        );

        return $managementNoticeInformationList;
    }

    public function C00_9908_ApiResultMessage($messageId)
    {
        //API処理結果メッセージ取得
        //メッセージ = メッセージマスタ（メッセージ）
        //条件、メッセージID = メッセージID
        $messageMaster = $this->getEntityService()->getFirstData(
            'MessageMaster',
            array(
                'conditions' => array(
                    'messageId' => $messageId
                ),
                'selects' => array('message')
            )
        );

        return $messageMaster;
    }

    public function C00_9801_DmmUserState($dmmId)
    {
//        ユーザ状態チェック
//            ユーザー状態 = DMMユーザー情報(DMMユーザー状態)
//            条件、DMMユーザーID = DMMユーザーID
        $dmmUserState = $this->getEntityService()->getFirstData(
            'DmmUserInformation',
            array(
                'conditions' => array(
                    'dmmId' => $dmmId
                ),
                'selects' => array('dmmIdState')
            )
        );

//        ユーザー状態 = 2 の場合、処理結果 = 980101 として、処理を終了する。
        if ($dmmUserState == DmmUserInformation::DMM_USER_STATE_INTERRUPTION) {
            return array("resultCode" => "980101");
        }

//        ユーザー状態 = 4 の場合、処理結果 = 980102 として、処理を終了する。
        if ($dmmUserState == DmmUserInformation::DMM_USER_STATE_FINISHED) {
            return array("resultCode" => "980102");
        }

        return array("resultCode" => "");
    }

    public function C00_9939_SystemOperationSituationConfirmation($screenId)
    {
//        $now = DateUtil::getTimeNow();
//        $contentInformation = $this->getEntityService()->getFirstData(
//            'ContentInformation',
//            array(
//                'conditions' => array(
//                    'screenId' => $screenId,
//                    'periodStart' => array("<=" => $now),
//                    'periodEnd' => array(">" => $now)
//                ),
//                'selects' => array('contentInformation')
//            )
//        );
//
//        if($contentInformation){
//            return array("resultCode"=>"993901");
//        }
//
//        return array("resultCode"=>"");
    }

    public function C00_9941_UserStateConfirmation($userId)
    {
//        ユーザー状態確認
//            ユーザー状態 = ユーザー情報(ユーザー状態)
//            条件、ユーザーID = ユーザーID
        $userState = $this->getEntityService()->getFirstData(
            'UserInformation',
            array(
                'conditions' => array(
                    'userId' => $userId
                ),
                'selects' => array('userState')
            )
        );

//        ユーザー情報(ユーザー状態) = 1 の場合（アカウントBAN）、以下の処理を行う
//
//            処理結果 = 994101
//            処理を中断し、共通後処理を実施後、レスポンスする
        if ($userState == UserInformationConst::USER_STATE_STOP) {
            return array("resultCode" => "994101");
        }

        return array("resultCode" => "");
    }

    public function C00_9956_DmmUserUpdateHashValue($dmmId, $dmmUserHash)
    {
//        DMMユーザー情報のレコード更新
//            DMMユーザー情報（端末ハッシュ値 - dmmUserHash） = 端末ハッシュ値
//            条件、DMMユーザーID = DMMユーザーID
        $entityService = $this->getEntityService();
        $entityService->dqlUpdate(
            'DmmUserInformation',
            array(
                'update' => array(
                    'dmmUserHash' => $dmmUserHash
                ),
                'conditions' => array(
                    'dmmId' => $dmmId
                )
            )
        );

        return (array("resultCode" => ""));
    }

    public function C00_9955_DmmUserUpdateState($dmmId, $dmmIdState)
    {
        $timeNow = DateUtil::getTimeNow();

//        DMMユーザー状態より、更新項目を決定する
//            DMMユーザー状態より、現在日時を指定する項目を決定する
//                1.ゲーム利用開始　　→　開始日時　　　※あえて書きますが、ここではないはず・・・
//                2.アプリ利用中断　　→　中断日時
//                3.アプリ利用再開　　→　再開日時
//                4.ゲーム利用終了　 →　終了日時
        $updateArray = array(
            DmmUserInformationConst::DMM_USER_STATE_STARTED => array(
                'dmmIdState'    => $dmmIdState,
                'addTime'       => $timeNow
            ),
            DmmUserInformationConst::DMM_USER_STATE_INTERRUPTION => array(
                'dmmIdState'    => $dmmIdState,
                'suspendTime'   => $timeNow
            ),
            DmmUserInformationConst::DMM_USER_STATE_RESUME => array(
                'dmmIdState'    => $dmmIdState,
                'resumeTime'    => $timeNow
            ),
            DmmUserInformationConst::DMM_USER_STATE_FINISHED => array(
                'dmmIdState'    => $dmmIdState,
                'removeTime'    => $timeNow
            )
        );

//        DMMユーザー情報のレコード更新
//            DMMユーザー情報（DMMユーザー状態, 上記で決めた日時） = DMMユーザー状態, 現在日時
//            条件、DMMユーザーID = DMMユーザーID
        $entityService = $this->getEntityService();
        $entityService->dqlUpdate(
            'DmmUserInformation',
            array(
                'update'        => $updateArray[$dmmIdState],
                'conditions'    => array(
                'dmmId'         => $dmmId
                )
            )
        );

        return (array("resultCode" => ""));

    }

}
