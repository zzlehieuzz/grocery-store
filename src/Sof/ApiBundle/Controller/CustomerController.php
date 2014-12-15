<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\Customer;
use Sof\ApiBundle\Lib\DateUtil;

class CustomerController extends BaseController
{

    /**
     * @Route("/Customer_Load", name="Customer_Load")
     */
    public function Customer_LoadAction()
    {
        $params = $this->getPagingParams();
        $arrEntity = $this->getEntityService()->getDataForPaging(
            'Customer',
            array('orderBy' => array('id' => 'DESC'),
                'firstResult' => $params['start'],
                'maxResults' => $params['limit']));

        return $this->jsonResponse(array('data' => $arrEntity['data']), $arrEntity['total']);
    }

    /**
     * @Route("/Customer_LoadAll", name="Customer_LoadAll")
     */
    public function Customer_LoadAllAction()
    {
        $arrEntity = $this->getEntityService()->getAllData('Customer', array('orderBy' => array('id' => 'DESC')));

        return $this->jsonResponse(array('data' => $arrEntity));
    }

    /**
     * @Route("/Customer_Update", name="Customer_Update")
     */
    public function Customer_UpdateAction()
    {
      $entityService = $this->getEntityService();
      $params        = $this->getJsonParams();

      if ($params['id'] != 0) {
        $entityService->dqlUpdate(
          'Customer',
          array('update' => $params,
            'conditions' => array('id' => $params['id'])
          )
        );
        $entityService->completeTransaction();
      } else {
        $entityService->rawSqlInsert('Customer', array('insert' => $params));
      }

      return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Customer_Delete", name="Customer_Delete")
     */
    public function Customer_DeleteAction()
    {
        $entityService = $this->getEntityService();

        $params = $this->getJsonParams();

        $entityService->dqlDelete(
            'Customer',
            array(
                'conditions' => array(
                    'id'   => $params,
                )
            )
        );
        $entityService->completeTransaction();

        return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Customer_LoadLastCode", name="Customer_LoadLastCode")
     */
    public function Customer_LoadLastCodeAction()
    {
        $dateCurrent = DateUtil::getCurrentDate(DateUtil::FORMAT_DATE_YMD_NOT);
        $code = 'KH';
        $arrCustomer = $this->getEntityService()->getFirstData(
            'Customer',
            array(
                'selects'    => array('code'),
                'orderBy'    => array('id' => 'DESC'),
                'conditions' => array('code' => array('LIKE' => "$code/%"))
            ));
        if ($arrCustomer) {
            $oldCode = $arrCustomer;
            $arrTemp = explode('/', $oldCode);
            if (isset($arrTemp[2])) {
                $arrTemp[1] = $dateCurrent;
                $arrTemp[2]++;
            }
            $invoiceCodeNew = implode('/', $arrTemp);
        } else {
            $invoiceCodeNew = $code.'/'.$dateCurrent.'/1';
        }

        return $this->jsonResponse(array('data' => $invoiceCodeNew));
    }
}