<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\User;
use Sof\ApiBundle\Entity\ValueConst\InvoiceConst;
use Sof\ApiBundle\Lib\DateUtil;

class InvoicesController extends BaseController
{
    //LIST INVOICE

    /**
     * @Route("/Invoice_Load", name="Invoice_Load")
     */
    public function Invoice_LoadAction()
    {
        $request      = $this->getRequestData();
        $params       = $this->getPagingParams();
        $arrSubject   = array();
        $arrCondition = array();

        $invoiceType   = $request->get('invoiceType');
        $fromDate      = $request->get('fromDate');
        $toDate        = $request->get('toDate');
        $customerName  = '%' .$request->get('customerName').'%';
        $invoiceNumber = $request->get('invoiceNumber');

        $entityService = $this->getEntityService();

        if ($fromDate && $toDate) {
            $arrCondition['createInvoiceDate'] = array('>=' => $fromDate,
                                                       '<=' => $toDate);
        } else {
            if ($fromDate) {
                $arrCondition['createInvoiceDate'] = array('>=' => $fromDate);
            }

            if ($toDate) {
                $arrCondition['createInvoiceDate'] = array('<=' => $toDate);
            }
        }

        if ($invoiceNumber != "") {
            $arrCondition['invoiceNumber'] = array('LIKE' => '%' . $invoiceNumber . '%');
        }

        if ($request->get('customerName') != "") {
            $arrDistributorId = $entityService->getAllData(
                'Distributor',
                array(
                    'selects'    => array('id'),
                    'orderBy'    => array('id' => 'DESC'),
                    'conditions' => array('name' => array('LIKE' => $customerName))
                ));

            if ($arrDistributorId) {
                foreach($arrDistributorId as $dis){
                    $arrSubject['invoiceType1'][] = ($dis['id']);
                }
            }

            $arrCustomerId = $entityService->getAllData(
                'Customer',
                array(
                    'selects'    => array('id'),
                    'orderBy'    => array('id' => 'DESC'),
                    'conditions' => array('name' => array('LIKE' => $customerName))
                ));

            if ($arrCustomerId) {
                foreach($arrCustomerId as $cus){
                    $arrSubject['invoiceType2'][] = ($cus['id']);
                }
            }

            $arrEntity['data'] = array();

            if (isset($arrSubject['invoiceType1']) &&  $arrSubject['invoiceType1'] && $invoiceType != 2) {
                $arrCondition['invoiceType'] = 1;
                $arrCondition['subject'] = array('IN' => $arrSubject['invoiceType1']);

                $arrEntity0 = $entityService->getDataForPaging(
                    'Invoice',
                    array('conditions'  => $arrCondition,
                        'orderBy'     => array('id' => 'DESC'),
                        'firstResult' => $params['start'],
                        'maxResults' => $params['limit']
                    ));

                if ($arrEntity0['data']) {
                    $arrEntity['data'] = array_merge ($arrEntity['data'], $arrEntity0['data']);
                }
            }

            if (isset($arrSubject['invoiceType2']) && $arrSubject['invoiceType2'] && $invoiceType != 1) {
                $arrCondition['invoiceType'] = 2;
                $arrCondition['subject']     = array('IN' => $arrSubject['invoiceType2']);

                $arrEntity1 = $entityService->getDataForPaging(
                    'Invoice',
                    array('conditions'  => $arrCondition,
                          'orderBy'     => array('id' => 'DESC'),
                          'firstResult' => $params['start'],
                          'maxResults'  => $params['limit']
                    ));

                if ($arrEntity1['data']) {
                    $arrEntity['data'] = array_merge ($arrEntity['data'], $arrEntity1['data']);
                }
            }
        } else {
            if ($invoiceType == 1 || $invoiceType == 2) {
                $arrCondition['invoiceType'] = $invoiceType;
            }

            $arrEntity = $entityService->getDataForPaging(
                'Invoice',
                array('conditions'  => $arrCondition,
                      'orderBy'     => array('id' => 'DESC'),
                      'firstResult' => $params['start'],
                      'maxResults'  => $params['limit']
                ));
        }
        $arrData = array();
        if ($arrEntity['data']) {
            foreach($arrEntity['data'] as $key=>$entity){
                $subjectName = "";
                if ($entity['invoiceType'] == 1) {
                    $entitySubject = $entityService->getFirstData(
                        'Distributor',
                        array(
                            'conditions'  => array('id' => $entity['subject'])
                        ));

                } else {
                    $entitySubject = $entityService->getFirstData(
                        'Customer',
                        array(
                            'conditions'  => array('id' => $entity['subject'])
                        ));
                }

                if (count($entitySubject) > 0) {
                    $subjectName = $entitySubject['name'];
                }

                $arrData[$key]['id']                = $entity['id'];
                $arrData[$key]['subjectName']       = $subjectName;
                $arrData[$key]['invoiceType']       = $entity['invoiceType'];
                $arrData[$key]['invoiceTypeText']   = $entity['invoiceType'] == InvoiceConst::INVOICE_TYPE_1 ? InvoiceConst::INVOICE_TYPE_TEXT_1 : InvoiceConst::INVOICE_TYPE_TEXT_2;
                $arrData[$key]['invoiceNumber']     = $entity['invoiceNumber'];
                $arrData[$key]['createInvoiceDate'] = $entity['createInvoiceDate'] ? $entity['createInvoiceDate']->format('d/m/Y') : null;
                $arrData[$key]['paymentStatus']     = $entity['paymentStatus'];
                $arrData[$key]['deliveryStatus']     = $entity['deliveryStatus'];
                $arrData[$key]['description']       = $entity['description'];
                $arrData[$key]['amount']            = $entity['amount'].' VNĐ';
            }
        }

        return $this->jsonResponse(array('data' => $arrData, 'total' => count($arrData)));
    }

    /**
     * @Route("/Invoice_Delete", name="Invoice_Delete")
     */
    public function Invoice_DeleteAction()
    {
      $entityService = $this->getEntityService();
      $params = $this->getJsonParams();

      $entityService->dqlDelete(
        'Invoice',
        array(
          'conditions' => array(
            'id'   => $params,
          )
        )
      );
      $entityService->completeTransaction();

      return $this->jsonResponse(array('data' => $params));
    }

    //PHIEU NHAP
    /**
     * @Route("/Input_Load", name="Input_Load")
     */
    public function Input_LoadAction()
    {
        //Phiếu Nhập: type= 1
        $request = $this->getRequestData();
        $invoiceId = $request->get('id');
        $arrInvoiceDetail = array();
        $entityInvoice = array();

        if ($invoiceId) {
            $entityInvoice = $this->getEntityService()->getFirstData(
                'Invoice',
                array('orderBy'    => array('id' => 'DESC'),
                      'conditions' => array('id' => $invoiceId)));

            if ($entityInvoice['createInvoiceDate'] != "") {
                $entityInvoice['createInvoiceDate'] = $entityInvoice['createInvoiceDate'] ? $entityInvoice['createInvoiceDate']->format('d/m/Y') : null;
            } else {
                $entityInvoice['createInvoiceDate'] = null;
            }

            $arrInvoiceDetail = $this->getEntityService()->getAllData(
                'InvoiceDetail',
                array(
                    'orderBy'    => array('id' => 'DESC'),
                    'conditions' => array('invoiceId' => $invoiceId),
                    'firstResult' => $request->get('start'),
                    'maxResults' => $request->get('limit')
                ));

            $arrLiabilities = $this->getEntityService()->getAllData(
                'Liabilities',
                array(
                    'conditions' => array('invoiceId' => $invoiceId)
                ));

            $arrLiab = array();
            if (count($arrLiabilities)) {
                foreach ($arrLiabilities as $key=>$liability) {
                    $arrLiab[$key]['name'] =  $liability['name'];
                    $arrLiab[$key]['amount'] =  $liability['amount'];
                    $arrLiab[$key]['price'] =  $liability['price'];
                    $arrLiab[$key]['description'] =  $liability['description'];
                }
            }

            $entityInvoice['liab_arr'] = $arrLiab;

        } else {
            $invoiceNumberInput = $this->generatingInvoiceNumber(1);
            $invoiceNumberOutput = $this->generatingInvoiceNumber(2);

            return $this->jsonResponse(array('invoice_number' => array('input' => $invoiceNumberInput, 'output' => $invoiceNumberOutput)));
        }

        return $this->jsonResponse(array('grid_data' => $arrInvoiceDetail,
                                        'form_data' => $entityInvoice,
                                        'total' => count($arrInvoiceDetail)));
    }

    /**
     * @Route("/Input_Update", name="Input_Update")
     */
    public function Input_UpdateAction()
    {
        $entityService = $this->getEntityService();

        $params            =  $this->get('request')->getContent();
        $params            = json_decode($params);
        $formParent        = (array)$params->form_fields_value;
        $form_fields_value = (array)$formParent[0];
        $grid_value        = (array)$params->grid_value;
        $invoiceType       = $form_fields_value['invoiceType'];
        $arrProductPriceNew = array();
        $arrProductId       = array();

        $amount = 0;
        if ($grid_value){
            foreach ($grid_value as $rowValue) {
                $arrData = (array)$rowValue;
                $amount += $arrData['amount'];
            }
        }
        $form_fields_value['amount'] = $amount;

        if ($form_fields_value['invoiceNumber'] != "") {
            $arrCheckInvoice = $this->getEntityService()->getFirstData(
                'Invoice',
                array(
                    'conditions' => array('invoiceNumber' => $form_fields_value['invoiceNumber'])
                ));

            if ($arrCheckInvoice) {
                $invoiceId = $arrCheckInvoice['id'];
            }
        }

        if ($form_fields_value['id'] != "" || isset($invoiceId)) {
            //Update

            if ($form_fields_value['id']) {
                $invoiceId = $form_fields_value['id'];
                unset($form_fields_value['id']);
            }

            if ($invoiceId){
                $entityService->dqlUpdate(
                    'Invoice',
                    array('update'     => $form_fields_value,
                          'conditions' => array('id' => $invoiceId)
                    )
                );
            }
        } else {
            //Insert
            unset($form_fields_value['id']);
            if($form_fields_value['invoiceType'] == InvoiceConst::INVOICE_TYPE_1) {
                $form_fields_value['paymentStatus']  = 0;
                $form_fields_value['deliveryStatus'] = 0;
            } else {
                $form_fields_value['paymentStatus']  = InvoiceConst::PAYMENT_STATUS_1;
                $form_fields_value['deliveryStatus'] = InvoiceConst::DELIVERY_STATUS_1;
            }

            $invoiceId = $entityService->rawSqlInsert('Invoice', array('insert' => $form_fields_value));
        }

        //Update Invoice Detail
        $arrInvoiceDetail = $this->getEntityService()->getAllData(
            'InvoiceDetail',
            array(
                'orderBy'    => array('id' => 'DESC'),
                'conditions' => array('invoiceId' => $invoiceId)
            ));

        if (count($grid_value) > 0) {
            $arrInsert = array();
            $arrUpdate = array();
            $arrDelete = array();
            $arrNewId  = array();

            foreach ($grid_value as $key=>$rowValue) {
                $arrData = (array)$rowValue;

                if ($arrData['id'] == 0) {
                    unset($arrData['id']);
                    $arrData['invoiceId'] = $invoiceId;
                    $arrInsert[] = $arrData;
                } else {
                    $arrNewId[] = $arrData['id'];
                }

                if (count($arrInvoiceDetail) > 0 && isset($arrData['id']) && $arrData['id']) {
                    for ($i = 0; $i < count($arrInvoiceDetail); $i++){
                        if ($arrInvoiceDetail[$i]['id'] == $arrData['id']) {
                            $arrData['invoiceId'] = $invoiceId;
                            $arrUpdate[] = $arrData;
                        }
                    }
                }

                if ($invoiceType == 2) {
                    $arrProductId[]                         = $arrData['productId'];
                    $arrProductPriceNew[$key]['product_id'] = $arrData['productId'];
                    $arrProductPriceNew[$key]['price']      = $arrData['price'];
                }
            }

            if (count($arrInvoiceDetail) > 0) {
                if ($arrNewId) {
                    foreach ($arrInvoiceDetail as $itemDetail) {
                        if (!in_array($itemDetail['id'], $arrNewId)) {
                            $arrDelete['id'] = $itemDetail['id'];
                        }
                    }
                }
            }

            //Update
            if (count($arrUpdate)) {
                foreach ($arrUpdate as $arrEntity0){
                    $id = $arrEntity0['id'];
                    unset($arrEntity0['id']);
                    $entityService->dqlUpdate(
                        'InvoiceDetail',
                        array('update'     => $arrEntity0,
                              'conditions' => array('id' => $id)
                        )
                    );
                }
            }

            //Insert
            if (count($arrInsert)) {
                foreach ($arrInsert as $arrEntity){
                    $invoiceId = $entityService->rawSqlInsert('InvoiceDetail', array('insert' => $arrEntity));
                }
            }

            //Delete
            if (count($arrDelete)) {
                foreach ($arrDelete as $itemDel){
                    $entityService->delete('InvoiceDetail', $itemDel);
                }
            }

            //Update price of product
            $arrProductUpdate = array();
            if ($arrProductId) {
                foreach ($arrProductId as $proId) {
                    $listProduct = $this->getEntityService()->getFirstData(
                        'Product',
                        array(
                            'conditions' => array('id' => $proId)
                        ));

                    foreach ($arrProductPriceNew as $priceNew) {
                        if ($priceNew['product_id'] == $proId && $priceNew['price'] != $listProduct['salePrice']) {
                            $arrProductUpdate[] = $priceNew;
                        }
                    }
                }

                if (count($arrProductUpdate)) {
                    foreach ($arrProductUpdate as $proUpdate) {
                        $entityService->dqlUpdate(
                            'Product',
                            array('update'     => array('salePrice' => $proUpdate['price']),
                                    'conditions' => array('id' => $proUpdate['product_id'])
                            )
                        );
                    }
                }
            }

            $entityService->completeTransaction();
        }

        return $this->jsonResponse(array('data' => array()));
    }

    /**
     * @Route("/Input_Delete", name="Input_Delete")
     */
    public function Input_DeleteAction()
    {
        $entityService = $this->getEntityService();

        $params = $this->getJsonParams();

        $entityService->dqlDelete(
            'InvoiceDetail',
            array(
                'conditions' => array(
                    'id'   => $params,
                )
            )
        );

        $entityService->dqlDelete(
            'Invoice',
            array(
                'conditions' => array(
                    'id'   => $params,
                )
            )
        );

        $entityService->completeTransaction();

        return $this->jsonResponse(array('data' => $params));
    }

    private function generatingInvoiceNumber($invoiceType){
        $dateCurrent = DateUtil::getCurrentDate(DateUtil::FORMAT_DATE_YMD_NOT);

        if ($invoiceType == 1) {
            $invoiceNumber = 'PN';
        } else {
            $invoiceNumber = 'PX';
        }

        $entityInvoice = $this->getEntityService()->getFirstData(
            'Invoice',
            array(
                'orderBy'     => array('id' => 'DESC'),
                'conditions'  => array('invoiceType' => $invoiceType)
            ));

        if ($entityInvoice) {
            $oldInvoiceNumber = $entityInvoice['invoiceNumber'];
            $arrTemp = explode('/', $oldInvoiceNumber);
            $newNum = 1;
            if (isset($arrTemp[2])) {
                $newNum = (int)$arrTemp[2] + 1;
            }

            $invoiceNumberNew = $invoiceNumber.'/'.$dateCurrent.'/'.$newNum;
        } else {
            $invoiceNumberNew = $invoiceNumber.'/'.$dateCurrent.'/1';
        }

        return $invoiceNumberNew;

    }

    /**
     * @Route("/List_Output_Load", name="List_Output_Load")
     */
    public function List_Output_LoadAction()
    {
        $listInvoice = array();

        $entityInvoice = $this->getEntityService()->getAllData(
            'Invoice',
            array(
                'orderBy'     => array('id' => 'DESC'),
                'conditions'  => array('invoiceType' => 2)
            ));

        if (count($entityInvoice)){
            foreach ($entityInvoice as $key=>$item) {

                $listInvoice[$key]['id'] = $item['id'];
                $listInvoice[$key]['invoiceNumber'] = $item['invoiceNumber'];

                if ($item['createInvoiceDate'] != "") {
                    $listInvoice[$key]['createInvoiceDate'] = $item['createInvoiceDate'];
                } else {
                    $listInvoice[$key]['createInvoiceDate'] = null;
                }

                $listInvoice[$key]['address'] = $item['address'];
                $listInvoice[$key]['phoneNumber'] = $item['phoneNumber'];
                $listInvoice[$key]['invoiceType'] = $item['invoiceType'];
                $listInvoice[$key]['totalAmount'] = $item['totalAmount'];
                $listInvoice[$key]['description'] = $item['description'];

                //Customer Info
                $arrCustomer = $this->getEntityService()->getFirstData(
                    'Customer',
                    array(
                        'orderBy'    => array('id' => 'DESC'),
                        'conditions' => array('id' => $item['subject'])
                    ));

                $listInvoice[$key]['customerCode'] = $arrCustomer['code'];
                $listInvoice[$key]['customerName'] = $arrCustomer['name'];

                //InvoiceDetail Info
                $arrInvoiceDetail = $this->getEntityService()->getAllData(
                    'InvoiceDetail',
                    array(
                        'orderBy'    => array('id' => 'DESC'),
                        'conditions' => array('invoiceId' => $item['id'])
                    ));

                foreach ($arrInvoiceDetail as $keyDetail => $itemDetail) {
                    //Product Info
                    $arrProduct = $this->getEntityService()->getFirstData(
                        'Product',
                        array(
                            'orderBy'    => array('id' => 'DESC'),
                            'conditions' => array('id' => $itemDetail['productId'])
                        ));

                    //Unit Info
                    $arrUnit = $this->getEntityService()->getFirstData(
                        'Unit',
                        array(
                            'orderBy'    => array('id' => 'DESC'),
                            'conditions' => array('id' => $itemDetail['unit'])
                        ));

                    //Liabilities Info
                    $arrLiabilities = $this->getEntityService()->getAllData(
                        'Liabilities',
                        array(
                            'conditions' => array('invoiceId' => $item['id'])
                        ));

                    $arrLiab = array();
                    if (count($arrLiabilities)) {
                        foreach ($arrLiabilities as $key1=>$liability) {
                            $arrLiab[$key1]['name'] =  $liability['name'];
                            $arrLiab[$key1]['amount'] =  $liability['amount'];
                            $arrLiab[$key1]['price'] =  $liability['price'];
                            $arrLiab[$key1]['description'] =  $liability['description'];
                        }
                    }

                    $listInvoice[$key]['invoiceId'][$keyDetail]['unitCode'] = $arrUnit['code'];
                    $listInvoice[$key]['invoiceId'][$keyDetail]['unitName'] = $arrUnit['name'];
                    $listInvoice[$key]['invoiceId'][$keyDetail]['productCode'] = $arrProduct['code'];
                    $listInvoice[$key]['invoiceId'][$keyDetail]['productName'] = $arrProduct['name'];
                    $listInvoice[$key]['invoiceId'][$keyDetail]['quantity'] = $itemDetail['quantity'];
                    $listInvoice[$key]['invoiceId'][$keyDetail]['price'] = $itemDetail['price'];
                    $listInvoice[$key]['invoiceId'][$keyDetail]['amount'] = $itemDetail['amount'];
                    $listInvoice[$key]['invoiceId'][$keyDetail]['liab_arr'] = $arrLiab;
                }
            }
        }

        return $this->jsonResponse(array('data' => $listInvoice));
    }

    /**
     * @Route("/Invoice_LoadDataToPrint", name="Invoice_LoadDataToPrint")
     */
    public function Invoice_LoadDataToPrintAction()
    {
        $params = $this->getJsonParams();

        if($params) {
//            print_r($params);
        }

        return $this->jsonResponse(array('data' => $params));
    }

}