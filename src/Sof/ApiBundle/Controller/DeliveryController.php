<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\User;
use Sof\ApiBundle\Entity\ValueConst\InvoiceConst;
use Sof\ApiBundle\Lib\DateUtil;

class DeliveryController extends BaseController
{

    /**
     * @Route("/Delivery_InvoiceLoad", name="Delivery_InvoiceLoad")
     */
    public function Delivery_InvoiceLoadAction()
    {
        $entityService    = $this->getEntityService();
        $arrDriverInvoice = $entityService->getAllData('DriverInvoice', array('selects' => array('invoiceId')));

        $arr = array();
        foreach ($arrDriverInvoice as $item) {
            $arr[] = $item['invoiceId'];
        }

        $arrData = $entityService->selectOnDefault('Invoice:getDataInvoice_Delivery', $arr);

        return $this->jsonResponse(array('data' => $arrData));
    }

    /**
     * @Route("/Delivery_DriverInvoiceLoad", name="Delivery_DriverInvoiceLoad")
     */
    public function Delivery_DriverInvoiceLoadAction()
    {
        $request  = $this->getRequestData();
        $driverId = $request->get('driverId');
        $arrData  = array();
        if ($driverId) {
            $entityService = $this->getEntityService();
            $arrData = $entityService->selectOnDefault('DriverInvoice:getData_Delivery', $driverId);
        }

        return $this->jsonResponse(array('data' => $arrData));
    }

    /**
     * @Route("/Delivery_AddDriverInvoice", name="Delivery_AddDriverInvoice")
     */
    public function Delivery_AddDriverInvoiceAction()
    {
        $params = $this->getJsonParams();

        if (isset($params['driverId']) && ($driverId = $params['driverId'])
            && isset($params['data']) && ($data = $params['data'])) {
            $entityService = $this->getEntityService();
            $arrData = array('driverId' => $driverId);
            foreach($data as $paramItem) {
                $arrDriverInvoice = $arrData;
                $arrDriverInvoice['invoiceId'] = $paramItem;
                $entityService->rawSqlInsert('DriverInvoice', array('insert' => $arrDriverInvoice));
            }

//            $entityService->dqlUpdate(
//                'Invoice',
//                array('update' => array('deliveryStatus' => InvoiceConst::DELIVERY_STATUS_2),
//                      'conditions' => array('id' => $data)
//                )
//            );

            $entityService->completeTransaction();
        }

        return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Delivery_Delete", name="Delivery_Delete")
     */
    public function Delivery_DeleteAction()
    {
        $entityService = $this->getEntityService();
        $params        = $this->getJsonParams();
        if ($params) {
            $invoices = $entityService->getAllData(
                'DriverInvoice',
                array(
                    'selects'    => array('invoiceId'),
                    'conditions' => array('id' => $params),
                    'groupBy'    => array('invoiceId')
                ));

            $listInvoices = array();
            foreach ($invoices as $invoiceItem) {
                $listInvoices[] = $invoiceItem['invoiceId'];
            }
            if ($listInvoices) {
//                $entityService->dqlUpdate(
//                    'Invoice',
//                    array(
//                        'update' => array('deliveryStatus' => InvoiceConst::DELIVERY_STATUS_1),
//                        'conditions' => array('id' => $listInvoices)
//                    )
//                );

                $entityService->dqlDelete(
                    'DriverInvoice',
                    array('conditions' => array('id' => $params)));
            }
            $entityService->completeTransaction();
        }

        return $this->jsonResponse(array('data' => $params));
    }
}