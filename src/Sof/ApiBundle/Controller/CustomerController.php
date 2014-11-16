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

        $params = $this->getPagingParams();
        $arrEntity = $this->getEntityService()->getDataForPaging(
            'Customer',
            array('orderBy' => array('id' => 'DESC'),
                'firstResult' => $params['start'],
                'maxResults' => $params['limit']));

        return $this->jsonResponse(array('data' => $arrEntity['data']), $arrEntity['total']);
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
}