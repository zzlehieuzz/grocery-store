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
}