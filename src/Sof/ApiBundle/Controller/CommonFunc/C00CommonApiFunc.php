<?php

namespace Sof\ApiBundle\Controller\CommonFunc;

use Sof\ApiBundle\Entity\BaseEntity;
use Sof\ApiBundle\Entity\ManagementNoticeInformation;
use Sof\ApiBundle\Entity\ValueConst\ContentInformationConst;
use Sof\ApiBundle\Entity\ValueConst\HelpMasterConst;
use Sof\ApiBundle\Entity\ValueConst\UserInformationConst;
use Sof\ApiBundle\Exception\SofApiException;
use Sof\ApiBundle\Lib\DateUtil;
use Sof\ApiBundle\Service\EntityService;
use Sof\LogBundle\Service\LogDatabaseService;

trait C00CommonApiFunc
{
    use BaseFunc;

    public function C00_9981_UserAuthenticate($userId, $authNumber)
    {
        //ユーザー認証情報確認
        //ユーザー認証情報 = ユーザー認証情報（ユーザーID）
        //条件、ユーザーID = ユーザーID AND 認証番号 = 認証番号
        $userAuthenticate = $this->getEntityService()->getFirstData(
            'UserAuthenticationInformation',
            array(
                'conditions' => array(
                    'userId' => $userId,
                    'authNumber' => $authNumber
                ),
                'selects' => array('userId')
            ),EntityService::MASTER
        );

        //ユーザー認証情報の取得件数が0件の場合、処理結果 = 998101 として、処理を終了する。
        if (!$userAuthenticate) {
            throw new SofApiException('998101');
        }

        return $userAuthenticate;
    }

    public function C00_9939_SystemOperationSituationConfirmation()
    {
        //システム運用状況確認
        //システム運用マスタ(システム設定) = 0 の場合、以下の処理を行う。
        $systemOperation = $this->getEntityService()->getFirstData(
            'SystemOperationMaster',
            array(
                'selects' => array('systemSetting')
            )
        );

        //処理結果 = 993901
        //処理を中断し、（必要であれば）共通後処理を実施後、レスポンスする
        if (!$systemOperation) {
            throw new SofApiException('993901');
        }
    }

    public function C00_9941_UserStateConfirmation($userId)
    {
        //ユーザー状態確認
        //ユーザー状態 = ユーザー情報(ユーザー状態)
        //条件、ユーザーID = ユーザーID
        $userState = $this->getEntityService()->getFirstData(
            'UserInformation',
            array(
                'conditions' => array(
                    'userId' => $userId
                ),
                'selects' => array('userState')
            ),EntityService::MASTER
        );

        //ユーザー情報(ユーザー状態) = 1 の場合（アカウントBAN）、以下の処理を行う

        //処理結果 = 994101
        //処理を中断し、共通後処理を実施後、レスポンスする
        if ($userState == UserInformationConst::USER_STATE_STOP) {
            throw new SofApiException('994101');
        }
    }

    public function C00_9904_UserVolumeInformation($userId)
    {
        //ユーザー音量情報リスト = ユーザー音量情報(ゲーム音量SE, ゲーム音量BGM, ゲーム音量音声, ミュート)
        // 条件、ユーザーID = ユーザーID
        $userVolumeInformation = $this->getEntityService()->getFirstData(
            'UserVolumeInformation',
            array(
                'conditions' => array(
                    'userId' => $userId
                ),
                'selects' => array('gameVolumeSe', 'gameVolumeBgm', 'gameVolumeVoice', 'mute')
            ),EntityService::MASTER
        );

        return $userVolumeInformation;
    }

    public function C00_9982_UserVolumeInformationUpdateProcess($userId, $gameVolumeSe, $gameVolumeBgm, $gameVolumeVoice, $mute)
    {
        //ユーザー音量設定情報更新
        //ユーザー音量情報(ゲーム音量SE, ゲーム音量BGM, ゲーム音量音声, ミュート) = ゲーム音量SE, ゲーム音量BGM, ゲーム音量音声, ミュート
        //条件、ユーザーID = ユーザーID
        $entityService = $this->getEntityService();
        $entityService->dqlUpdate(
            'UserVolumeInformation',
            array(
                'update' => array(
                    'gameVolumeSe'      => $gameVolumeSe,
                    'gameVolumeBgm'     => $gameVolumeBgm,
                    'gameVolumeVoice'   => $gameVolumeVoice,
                    'mute' => $mute
                ),
                'conditions' => array(
                    'userId' => $userId
                )
            )
        );

        return array();
    }

    public function C00_9905_ScreenParts($screenId, $flg)
    {
        //画面部品取得
        //1.フラグ = 1 の場合
        //コンテンツ情報リスト = コンテンツ情報（画面ID, 管理ID, コンテンツ情報）
        //条件、画面ID = 画面ID AND 期間開始 ≦ 現在日時 < 期間終了 AND フラグ in (0, 2)
        $timeNow = DateUtil::getTimeNow();
        if ($flg == BaseEntity::FLAG_ON) {
            $screenPartsList = $this->getEntityService()->getAllData(
                'ContentInformation',
                array(
                    'conditions' => array(
                        'screenId' => $screenId,
                        'periodStart' => array('<=' => $timeNow),
                        'periodEnd' => array('>' => $timeNow),
                        'flg' => array('IN' => array(ContentInformationConst::FLG_DEFAULT, ContentInformationConst::FLG_EVENT))
                    ),
                    'selects' => array('screenId', 'screenName', 'managementId', 'contentInformation')
                )
            );
        } else {
            //2.フラグ != 1 の場合 (通常時)
            //コンテンツ情報リスト = コンテンツ情報（画面ID, 管理ID, コンテンツ情報）
            //条件、画面ID = 画面ID AND 期間開始 ≦ 現在日時 < 期間終了 AND フラグ < 2
            $screenPartsList = $this->getEntityService()->getAllData(
                'ContentInformation',
                array(
                    'conditions' => array(
                        'screenId' => $screenId,
                        'periodStart' => array('<=' => $timeNow),
                        'periodEnd' => array('>' => $timeNow),
                        'flg' => array('<' => ContentInformationConst::FLG_EVENT)
                    ),
                    'selects' => array('screenId', 'screenName', 'managementId', 'contentInformation')
                )
            );
        }
        return $screenPartsList;
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
                'selects' => array('bannerInformationValue','content'),
                'orderBy' => array('periodStart' => 'DESC')
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
        $footerInformationList = array();
        $footerInformationList['bannerList'] = $bannerInformationList;
        $footerInformationList['operationNoticeList'] = $managementNoticeList['operationNoticeList'];
        $footerInformationList['helpMasterList'] = $helpMasterList;

        return $footerInformationList;
    }

    public function C00_9907_ManagementNoticeInformation()
    {
        $now = DateUtil::getTimeNow();
        /*
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
                );*/

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
                'selects' => array('noticeDate', 'noticeContent'),
                'orderBy' => array('noticeDate' => 'DESC')
            )
        );

        $managementNoticeInformationList = array(
            'operationNoticeList' => $operationNoticeList,
//            'maintenanceNoticeList' => $maintenanceNoticeList
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

    public function C00_9966_LogRecord($userId,$apiNo,$result,$logClassification,$argument)
    {
        //1	全ユーザー行動ログへのレコード追加
        //ログ種別 = 3 の場合は、「3.ワーニングログへのレコード追加」処理へ
        //
        //全ユーザー行動ログ（*）
        //日時 = 現在日時
        //ユーザーID = ユーザーID
        //APINo = APINo
        //処理結果 = 処理結果
        //引数 = 引数
        if($logClassification != LogDatabaseService::WARNING){
            $this->getLoggerService()->actionLog($userId, $apiNo, $result, $argument);
        }

        //2エラーログへのレコード追加
        //ログ種別 != 2 の場合は、「3.ワーニングログへのレコード追加」処理へ
        //
        //エラーログ(*)
        //日時 = 現在日時
        //ユーザーID = ユーザーID
        //APINo = APINo
        //処理結果 = 処理結果
        //引数 = 引数
        if($logClassification == LogDatabaseService::ERROR){
            $this->getLoggerService()->errorLog($userId, $apiNo, $result, $argument);
        }

        //3ワーニングログへのレコード追加
        //ログ種別 != 3 の場合は、処理を終了する
        if($logClassification != LogDatabaseService::WARNING){
            return array();
        }

        //ログ設定 = システム運用マスタ（ログ）
        //条件、ID = 1
        $logSetting = $this->getEntityService()->getFirstData(
            'SystemOperationMaster',
            array(
                'conditions' => array(
                    'id' => 1
                ),
                'selects' => array('log')
            )
        );

        //ログ設定 != 1 の場合、処理を終了する
        //
        //ワーニングログ（*）
        //日時 = 現在日時
        //ユーザーID = ユーザーID
        //APINo = APINo
        //処理結果 = 処理結果
        //引数 = 引数
        if($logSetting == BaseEntity::FLAG_ON){
            $this->getLoggerService()->warningLog($userId, $apiNo, $result, $argument);
        }
        return array();
    }

    public function C00_9967_ChargeLogRecord($userId,$apiNo,$crystal,$goodsId)
    {
        //SF_TODO:datdvq cần cập nhật tài liệu mới
        $timeNow = DateUtil::getTimeNow();
        // 課金系ログへのレコード追加
        // 課金系ログ（*）
        // 日時 = 現在日時
        // ユーザーID = ユーザーID
        // APINo = APINo
        // 消費クリスタル数 = 消費クリスタル数      消費時数値入力を想定
        // 商品ID = 商品ID                  購入商品IDや、ガチャID
        $chargeLogParams = array(
            'date' => $timeNow,
            'userId'=> $userId,
            'apiNo'=> $apiNo,
            'crystal' => $crystal,
            'goodsId'=> $goodsId
        );
        //$this->getLoggerService()->chargeLog($userId,$apiNo,$chargeLogParams);

        return array();
    }
}
