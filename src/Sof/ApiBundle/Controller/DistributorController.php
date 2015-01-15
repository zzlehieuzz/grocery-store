<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Lib\DateUtil;

class DistributorController extends BaseController
{

    /**
     * @Route("/Distributor_Load", name="Distributor_Load")
     */
    public function Distributor_LoadAction()
    {
        $params = $this->getPagingParams();

        $arrEntity = $this->getEntityService()->getDataForPaging('Distributor',
            array('orderBy' => array('id' => 'DESC'),
                  'firstResult' => $params['start'],
                  'maxResults' => $params['limit']
            ));

        return $this->jsonResponse(array('data' => $arrEntity['data']), $arrEntity['total']);
    }

    /**
     * @Route("/Distributor_LoadAll", name="Distributor_LoadAll")
     */
    public function Distributor_LoadAllAction()
    {
        $arrEntity = $this->getEntityService()->getAllData('Distributor', array('orderBy' => array('id' => 'DESC')));

        return $this->jsonResponse(array('data' => $arrEntity));
    }

    /**
     * @Route("/Distributor_Update", name="Distributor_Update")
     */
    public function Distributor_UpdateAction()
    {
      $entityService = $this->getEntityService();
      $params        = $this->getJsonParams();

      if ($params['id'] != 0) {
        $entityService->dqlUpdate(
          'Distributor',
          array('update' => $params,
            'conditions' => array('id' => $params['id'])
          )
        );
        $entityService->completeTransaction();
      } else {
        $entityService->rawSqlInsert('Distributor', array('insert' => $params));
      }

      return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Distributor_Delete", name="Distributor_Delete")
     */
    public function Distributor_DeleteAction()
    {
        $entityService = $this->getEntityService();

        $params = $this->getJsonParams();

        $entityService->dqlDelete(
            'Distributor',
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
     * @Route("/Distributor_LoadLastCode", name="Distributor_LoadLastCode")
     */
    public function Distributor_LoadLastCodeAction()
    {
        $dateCurrent = DateUtil::getCurrentDate(DateUtil::FORMAT_DATE_YMD_NOT);
        $yearCurrent = DateUtil::getCurrentDate(DateUtil::FORMAT_DATE_Y);
        $code = 'PP';
        $arrDistributor = $this->getEntityService()->getFirstData(
            'Distributor',
            array(
                'selects'    => array('code'),
                'orderBy'    => array('id' => 'DESC'),
                'conditions' => array('code' => array('LIKE' => "$code/$yearCurrent%"))
            ));
        if ($arrDistributor) {
            $oldCode = $arrDistributor;
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