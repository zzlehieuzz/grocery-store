<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\User;
use Sof\ApiBundle\Entity\ValueConst\BaseConst;
use Sof\ApiBundle\Entity\ValueConst\InvoiceConst;
use Sof\ApiBundle\Lib\DateUtil;

class LiabilitiesController extends BaseController
{

    /**
     * @Route("/Liabilities_Load", name="Liabilities_Load")
     */
    public function Liabilities_LoadAction()
    {
        $request = $this->getRequestData();
        $id                = $request->get('id');
        $searchInvoiceName = $request->get('searchInvoiceName');

        $entityService = $this->getEntityService();
        $arrCustomer   = $entityService->selectOnDefault('Invoice:getData', $id, $searchInvoiceName);

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
     * @Route("/Liabilities_Name_Load", name="Liabilities_Name_Load")
     */
    public function Liabilities_Name_LoadAction()
    {
        $liabilities = $this->getEntityService()->getAllData('Liabilities',
            array('selects' => array('name'),
                  'groupBy' => array('name'),
                  'orderBy' => array('name')));

        return $this->jsonResponse(array('data' => $liabilities));
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

    /**
     * @Route("/Liabilities_AcceptAll", name="Liabilities_AcceptAll")
     */
    public function Liabilities_AcceptAllAction()
    {
        $params = $this->getJsonParams();

        if($params) {
            $this->getEntityService()->dqlUpdate(
                'Invoice',
                array('update' => array('paymentStatus'  => InvoiceConst::PAYMENT_STATUS_3,
                                        'deliveryStatus' => InvoiceConst::DELIVERY_STATUS_2),
                      'conditions' => array('id' => $params)
                )
            );

            $this->getEntityService()->completeTransaction();
        }

        return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Liabilities_AcceptDelivery", name="Liabilities_AcceptDelivery")
     */
    public function Liabilities_AcceptDeliveryAction()
    {
        $params = $this->getJsonParams();

        if($params) {
            $this->getEntityService()->dqlUpdate(
              'Invoice',
              array('update' => array('deliveryStatus' => InvoiceConst::DELIVERY_STATUS_2),
                    'conditions' => array('id' => $params)
              )
            );

            $this->getEntityService()->completeTransaction();
        }

        return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Liabilities_UnAcceptDelivery", name="Liabilities_UnAcceptDelivery")
     */
    public function Liabilities_UnAcceptDeliveryAction()
    {
        $params = $this->getJsonParams();

        if($params) {
            $this->getEntityService()->dqlUpdate(
              'Invoice',
              array('update' => array('deliveryStatus' => InvoiceConst::DELIVERY_STATUS_1),
                    'conditions' => array('id' => $params)
              )
            );

            $this->getEntityService()->completeTransaction();
        }

        return $this->jsonResponse(array('data' => $params));
    }

}