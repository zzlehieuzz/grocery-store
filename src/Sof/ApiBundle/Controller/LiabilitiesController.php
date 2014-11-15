<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\User;
use Sof\ApiBundle\Lib\DateUtil;

class LiabilitiesController extends BaseController
{

    /**
     * @Route("/Liabilities_Load", name="Liabilities_Load")
     */
    public function Liabilities_LoadAction()
    {
        $request = $this->getRequestData();
        $id      = $request->get('id');

        $entityService = $this->getEntityService();
        $arrCustomer   = $entityService->selectOnDefault('Invoice:getData', $id);

        return $this->jsonResponse(array('data' => $arrCustomer));
    }

    /**
     * @Route("/Liabilities_Customer_Load", name="Liabilities_Customer_Load")
     */
    public function Liabilities_Customer_LoadAction()
    {
        $entityService = $this->getEntityService();
        $arrCustomer   = $entityService->selectOnDefault('Customer:getData');

        return $this->jsonResponse(array('data' => $arrCustomer), count($arrCustomer));
    }

    /**
     * @Route("/Liabilities_Save", name="Liabilities_Save")
     */
    public function Liabilities_SaveAction()
    {
        $params        = $this->getJsonParams();
        $entityService = $this->getEntityService();
        if (isset($params['id']) && ($id = $params['id'])) {
            unset($params['id']);

            $entityService->dqlUpdate(
                'Liabilities',
                array('update' => $params,
                    'conditions' => array('id' => $id)
                )
            );
        } else {
            if (isset($params['id'])) unset($params['id']);
            if (isset($params['invoiceNumber']))  unset($params['invoiceNumber']);
            $id = $entityService->rawSqlInsert('Liabilities', array('insert' => $params));
        }
        $entityService->completeTransaction();
        $params['id'] = $id;

        return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Liabilities_Delete", name="Liabilities_Delete")
     */
    public function Liabilities_DeleteAction()
    {
        $entityService = $this->getEntityService();

        $params = $this->getJsonParams();

        $entityService->dqlDelete(
            'Liabilities',
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