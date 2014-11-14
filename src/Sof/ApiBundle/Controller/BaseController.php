<?php

namespace Sof\ApiBundle\Controller;

use Sof\ApiBundle\Lib\DateUtil;
use Sof\ApiBundle\Exception\SofApiException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class BaseController extends Controller implements FilterControllerInterface
{
    private $apiInput;

    private $clientCallback;

    /**
     * @return \Sof\ApiBundle\Service\EntityService
     */
    public function getEntityService()
    {
        return $this->get('entity_service');
    }

    /**
     * @return \Sof\ApiBundle\Controller\CommonFunc\CommonService
     */
    public function getCommonService()
    {
        return $this->get('common_service');
    }

    /**
     * Return api input and extra data
     * @param array $fields
     * @return array
     */
    public function getApiInput($fields = array())
    {
        return self::checkInput($this->apiInput, $fields);
    }

    public function getApiParams()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $this->clientCallback = $request->get('callback');

        $_controller = $request->get('_controller');

        preg_match("/.*::(.*)_(.*)_.*$/", $_controller, $controller_array);
        $result = array();
        if ($controller_array) {
            $result['controllerNo'] = $controller_array[1];
            $result['apiNo'] = $controller_array[2];
        }

        preg_match("/.*::(.*)$/", $_controller, $output_array);
        if ($output_array) {
            $result['actionName'] = $output_array[1];
        }

        $result['requestParams'] = urldecode($request->get('params'));

        $requestParams = json_decode($result['requestParams'], true);
        $result['requestParams'] = $requestParams;
        if ($requestParams) {
            $result = array_merge($result, $requestParams);
        }
        $this->apiInput = $result;

        return $result;
    }

    public function preAction()
    {
//        $apiInput = $this->getApiParams();

//        self::checkInput($apiInput, array('INT' => array('userId', 'authNumber')));
//
//        if(isset($apiInput['screenId'])) {
//            self::checkInput($apiInput, array('INT' => array('screenId')));
//        } else {
//            $apiInput['screenId'] = 0;
//        }

//        $commonService = $this->getCommonService();
        //ユーザー認証 SF_TODO:datdvq develop, no check
        //$commonService->C00_9981_UserAuthenticate($apiInput['userId'], $apiInput['authNumber']);
        //システム運用状況確認
//        $commonService->C00_9939_SystemOperationSituationConfirmation();
//        //ユーザー状態確認
//        $commonService->C00_9941_UserStateConfirmation($apiInput['userId']);
    }

    public function catchException(GetResponseForExceptionEvent $event)
    {
        $apiInput =  $this->apiInput;
        $exception = $event->getException();
        $resultCode = $apiInput['apiNo'] . 99;

        if($exception instanceof SofApiException && $exception->getMessage()) {
            $resultCode = $exception->getMessage();
        }

//        if (isset($apiInput['userId']) && !$exception->getCode()) {
//            $this->getCommonService()->C00_9966_LogRecord($apiInput['userId'], $apiInput['apiNo'], $resultCode, LogDatabaseService::ERROR, json_encode($apiInput['requestParams']));
//        };

        $event->setResponse($this->apiResponse(array(), $resultCode, true));
    }

    protected function apiResponse(array $data, $resultCode = '', $otherError = false, $isDebug = false)
    {
        $response = array();
        $apiInput  = $this->apiInput;
        $entityService = $this->getEntityService();
        $commonService = $this->getCommonService();

        try {
            $entityService->completeTransaction($otherError);

            if (!$resultCode) {
                $resultCode = $apiInput['apiNo'];
            }

            $response['resultCode'] = $resultCode;
            $response['apiResponse'] = $data;

            $commonResponse =  array('requestParams' => $apiInput['requestParams']);
            // 画面部品取得
            if (isset($apiInput['screenId'])) {
                $commonResponse['screenParts'] = $commonService->C00_9905_ScreenParts($apiInput['screenId'], 0);
            }
            $response['commonResponse'] = $commonResponse;

            // API処理結果メッセージ取得
            $response['resultMsg'] = '';
            if (strlen($resultCode) > 4) {
                $response['resultMsg'] = $this->getCommonService()->C00_9908_ApiResultMessage($resultCode);
            }

            if (isset($apiInput['userId'])) {
                $this->getCommonService()->C00_9966_LogRecord($apiInput['userId'], $apiInput['apiNo'], $resultCode, LogDatabaseService::ACTION, json_encode($apiInput['requestParams']));
            }
        } catch (\Exception $e) {
            $response = array('resultCode' => '99', 'resultMsg' => 'Post Action error.');
        }

        array_walk_recursive($response, function(&$item) {
                if ($item instanceof \DateTime) {
                    $item = $item->format(DateUtil::FORMAT_DATE_TIME);
                }
            });

        if ($this->get('kernel')->isDebug() || $isDebug) {
            return $this->render(
                '::debug_json.html.twig',
                array('data' => $response)
            );
        } else {
            $jsonP = new JsonResponse($response, 200);
            if ($this->clientCallback) {
                $jsonP->setCallback($this->clientCallback);
            }

            $jsonP->headers->set( 'X-Status-Code', 200 );
            return $jsonP;
        }
    }

    static private function checkInput($input = array(), $fields = array()) {
        if (isset($fields['INT'])) {
            foreach($fields['INT'] as $field) {
                if (!isset($input[$field]) || !ctype_digit(''.$input[$field])) {
                    unset($input[$field]);
                    throw new SofApiException;
                }

                if ($input[$field] > 2100000000) {
                    $input[$field] = 2100000000;
                }
            }
        }

        if (isset($fields['BIGINT'])) {
            foreach($fields['BIGINT'] as $field) {
                if (!isset($input[$field]) || !ctype_digit(''.$input[$field])) {
                    unset($input[$field]);
                    throw new SofApiException;
                }

                if ($input[$field] > 9220000000000000000) {
                    $input[$field] = 9220000000000000000;
                }
            }
        }

        if (isset($fields['CHAR'])) {
            foreach($fields['CHAR'] as $field) {
                if (!isset($input[$field]) || !preg_match('/^[A-Za-z0-9,]{0,}$/' , $input[$field])) {
                    unset($input[$field]);
                    throw new SofApiException;
                }

                if (strlen($input[$field]) > 255) {
                    $input[$field] = substr($input[$field], 0, 255);
                }
            }
        }

        if (isset($fields['VARCHAR'])) {
            foreach($fields['VARCHAR'] as $field) {
                if (!isset($input[$field]) || !preg_match('/^[A-Za-z0-9,]{0,}$/' , $input[$field])) {
                    unset($input[$field]);
                    throw new SofApiException;
                }

                if (strlen($input[$field]) > 65535) {
                    $input[$field] = substr($input[$field], 0, 65535);
                }
            }
        }

        if (isset($fields['EXIST'])) {
            foreach($fields['EXIST'] as $field) {
                if (!isset($input[$field])) {
                    throw new SofApiException;
                }
            }
        }

        if(isset($fields['DATE'])){
            foreach($fields['DATE'] as $field) {
                if (!DateUtil::validateDate($input[$field],'Y-m-d')) {
                    throw new SofApiException;
                }
            }
        }

        if(isset($fields['DATETIME'])){
            foreach($fields['DATETIME'] as $field) {
                if (!DateUtil::validateDate($input[$field])) {
                    throw new SofApiException;
                }
            }
        }

        return $input;
    }

    /**
     * @param array $data
     * @param bool $isError
     * @return JsonResponse
     *
     * @author HieuNLD 2014/06/13
     */
    protected function jsonResponse(array $data, $total = null, $isError = FALSE)
    {
        $result = array();
        $result['responseCode'] = $isError ? 204 : 200;
        $result['data'] = array();

        if ($total) {
            $result['total'] = $total;
        }

        if (isset($data['data']) && $data['data']) {
            $result['data'] = $data['data'];
        }

        if (isset($data['grid_data']) && $data['grid_data']) {
            $result['grid_data'] = $data['grid_data'];
        }

        if (isset($data['form_data']) && $data['form_data']) {
            $result['form_data'] = $data['form_data'];
        }

        if (isset($data['error']) && $data['error']) {
            $result['error'] = $data['error'];
        }

        return new JsonResponse($result);
    }

    /**
     * @return Object
     *
     * @author HieuNLD 2014/06/13
     */
    protected function getRequestData()
    {
        return $this->get('request');
    }

    /**
     * @return JsonResponse
     *
     * @author HieuNLD 2014/06/13
     */
    protected function getPagingParams()
    {
        $params  = array();
        $request = $this->get('request');

        $params['limit'] = $request->get('limit');
        $params['page']  = $request->get('page');
        $params['start'] = $request->get('start');
        $params['sort']  = $request->get('sort');
        $params['group'] = $request->get('group');

        return $params;
    }

    /**
     * @param string $key
     * @return array
     *
     * @author HieuNLD 2014/10/08
     */
    public function getJsonParams($key = 'params')
    {
        $params = array();
        $content = $this->getRequestData()->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true);
            $params = $params[$key];
        }

        return $params;
    }

    /**
     * @param string $key
     * @return array
     *
     * @author HieuNLD 2014/10/08
     */
    public function getJsonExtraData($key = 'extraData')
    {
        $params = array();
        $content = $this->getRequestData()->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true);
            $params = $params[$key];
        }

        return $params;
    }
}
