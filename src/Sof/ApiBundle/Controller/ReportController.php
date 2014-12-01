<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\Product;
use Sof\ApiBundle\Entity\ValueConst\InvoiceConst;
use Sof\ApiBundle\Lib\DateUtil;

class ReportController extends BaseController
{

    /**
     * @Route("/Report_InventoryLoad", name="Report_InventoryLoad")
     */
    public function Report_InventoryLoadAction()
    {
        $params   = $this->getPagingParams();
        $quantity = 0;
        $result2  = array();

        $fromDate    = $this->getRequestData()->get('fromDate');
        $toDate      = $this->getRequestData()->get('toDate');
        $productName = $this->getRequestData()->get('productName');

        $conditions = array();
        if ($productName) {
            $conditions['name'] = array('LIKE' => '%' . $productName . '%');
        }

        $entityService = $this->getEntityService();
        $arrEntity = $entityService->getDataForPaging('Product',
                      array('conditions'  => $conditions,
                            'orderBy'     => array('id' => 'DESC'),
                            'firstResult' => $params['start'],
                            'maxResults'  => $params['limit']));

        $arrInvoiceInput = $entityService->selectOnDefault(
            'InvoiceDetail:getData_ReportInventory', $fromDate, $toDate, InvoiceConst::INVOICE_TYPE_1);

        $arrInvoiceOutput = $entityService->selectOnDefault(
            'InvoiceDetail:getData_ReportInventory', $fromDate, $toDate, InvoiceConst::INVOICE_TYPE_2);

        $result1 = array();
        if ($arrInvoiceOutput) {
            foreach ($arrInvoiceOutput as $itemInvoiceOutput) {
                $result1[$itemInvoiceOutput['productId']]['quantity'] = $itemInvoiceOutput['quantity'];
            }
        }

        if ($arrInvoiceInput) {
            foreach ($arrInvoiceInput as $invoiceInputItem) {
                $inputQuantity  = $invoiceInputItem['quantity'];
                $outputQuantity = $quantity;
                if(isset($result1[$invoiceInputItem['productId']])) {
                    $outputQuantity = $result1[$invoiceInputItem['productId']]['quantity'];
                }
                $remainQuantity = $inputQuantity - $outputQuantity;

                $result2[$invoiceInputItem['productId']]['remainQuantity'] = $remainQuantity;
                $result2[$invoiceInputItem['productId']]['inputQuantity']  = $inputQuantity;
                $result2[$invoiceInputItem['productId']]['outputQuantity'] = $outputQuantity;
            }
        }
        foreach ($arrEntity['data'] as $key => $arrEntityItem) {
            $remainQuantity = $quantity;
            $inputQuantity  = $quantity;
            $outputQuantity = $quantity;
            if(isset($result2[$arrEntityItem['id']])) {
                $remainQuantity = $result2[$arrEntityItem['id']]['remainQuantity'];
                $inputQuantity  = $result2[$arrEntityItem['id']]['inputQuantity'];
                $outputQuantity = $result2[$arrEntityItem['id']]['outputQuantity'];
            }
            $arrEntity['data'][$key]['remainQuantity'] = $remainQuantity;
            $arrEntity['data'][$key]['inputQuantity']  = $inputQuantity;
            $arrEntity['data'][$key]['outputQuantity'] = $outputQuantity;
        }

        return $this->jsonResponse(array('data' => $arrEntity['data']), $arrEntity['total']);
    }

    /**
     * @Route("/Report_RevenueLoad", name="Report_RevenueLoad")
     */
    public function Report_RevenueLoadAction()
    {
        $fromDate = $this->getRequestData()->get('fromDate');
        $toDate   = $this->getRequestData()->get('toDate');

        $entityService = $this->getEntityService();

        $arrInvoiceInput = $entityService->selectOnDefault(
            'InvoiceDetail:getData_ReportRevenue', $fromDate, $toDate, InvoiceConst::INVOICE_TYPE_1);

        $arrInvoiceOutput = $entityService->selectOnDefault(
            'InvoiceDetail:getData_ReportRevenue', $fromDate, $toDate, InvoiceConst::INVOICE_TYPE_2);

//        print_r($arrInvoiceInput);
//        print_r($arrInvoiceOutput);
//        die;

//        for (i = 0; i < (n || 12); i++) {
//            data.push({
//                    name: Ext.Date.monthNames[i % 12],
//                    'input':100000000, 'data2':500050000, 'data3':(500050000-100000000), 'text': 'text' + i
//                });
//            }

        $data = array();

        $data[1]['name'] = '2014-01';
        $data[1]['input'] = 1000000;
        $data[1]['output'] = 3000000;
        $data[1]['remain'] = 2000000;

        $data[2]['name'] = '2014-02';
        $data[2]['input'] = 1000000;
        $data[2]['output'] = 3000000;
        $data[2]['remain'] = 2000000;

        $data[3]['name'] = '2014-03';
        $data[3]['input'] = 1000000;
        $data[3]['output'] = 3000000;
        $data[3]['remain'] = 2000000;

        $data[4]['name'] = '2014-04';
        $data[4]['input'] = 1000000;
        $data[4]['output'] = 3000000;
        $data[4]['remain'] = 2000000;

        $data[4]['name'] = '2014-05';
        $data[4]['input'] = 1000000;
        $data[4]['output'] = 3000000;
        $data[4]['remain'] = 2000000;

        $data[6]['name'] = '2014-06';
        $data[6]['input'] = 1000000;
        $data[6]['output'] = 3000000;
        $data[6]['remain'] = 2000000;

        return $this->jsonResponse(array('data' => $data), 0);
    }
}