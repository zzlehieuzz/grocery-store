<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\User;
use Sof\ApiBundle\Lib\DateUtil;

class InvoicesController extends BaseController
{
    //LIST INVOICE

    /**
     * @Route("/Invoice_Load", name="Invoice_Load")
     */
    public function Invoice_LoadAction()
    {
        $request = $this->getRequestData();

        //Search condition
        $arrCondition = array();

        $invoiceType = $request->get('invoiceType');
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');

        if ($invoiceType == 1 || $invoiceType == 2) {
            $arrCondition['invoiceType'] = $invoiceType;
        }

        if ($fromDate) {
            $arrCondition['createInvoiceDate'] = array('>=' => $fromDate);
        }

        if ($toDate) {
            $arrCondition['createInvoiceDate'] = array('<=' => $toDate);
        }

        $arrEntity = $this->getEntityService()->getAllData(
            'Invoice',
            array('conditions'  => $arrCondition,
                  'orderBy'     => array('id' => 'DESC')));

        $arrData = array();
        foreach($arrEntity as $key=>$entity){
            $subjectName = "";
            if ($entity['invoiceType'] == 1) {
                $entitySubject = $this->getEntityService()->getAllData(
                    'Distributor',
                    array(
                        'conditions'  => array('id' => $entity['subject'])
                    ));

            } else {
                $entitySubject = $this->getEntityService()->getAllData(
                    'Customer',
                    array(
                        'conditions'  => array('id' => $entity['subject'])
                    ));
            }

            if (count($entitySubject) > 0) {
                $subjectName = $entitySubject[0]['name'];
            }

            $paymentStatus = "";
            if ($entity['invoiceType'] == 2) {
                if ($entity['paymentStatus'] == 1) {
                    $paymentStatus = 'Đã Giao Hàng';
                } else {
                    $paymentStatus = 'Chưa Giao Hàng';
                }
            }

            $arrData[$key]['id'] = $entity['id'];
            $arrData[$key]['subjectName'] = $subjectName;
            $arrData[$key]['invoiceType'] = $entity['invoiceType'];
            $arrData[$key]['invoiceTypeText'] = $entity['invoiceType'] == 1 ? 'Phiếu Nhập': 'Phiếu Xuất';
            $arrData[$key]['invoiceNumber'] = $entity['invoiceNumber'];
            $arrData[$key]['createInvoiceDate'] = $entity['createInvoiceDate'] ? $entity['createInvoiceDate']->format('d-m-Y') : null;
            $arrData[$key]['paymentStatus'] = $paymentStatus;
            $arrData[$key]['amount'] = $entity['amount'].' VNĐ';
        }

        return $this->jsonResponse(array('data' => $arrData));
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
            $entityInvoice = $this->getEntityService()->getAllData(
                'Invoice',
                array(
                      'orderBy'     => array('id' => 'DESC'),
                      'conditions'  => array('id' => $invoiceId)
                ))[0];

            if ($entityInvoice['createInvoiceDate'] != "") {
                $entityInvoice['createInvoiceDate'] = $entityInvoice['createInvoiceDate']->format('d/m/Y');
            } else {
                $entityInvoice['createInvoiceDate'] = null;
            }

            $arrInvoiceDetail = $this->getEntityService()->getAllData(
                'InvoiceDetail',
                array(
                    'orderBy'    => array('id' => 'DESC'),
                    'conditions' => array('invoiceId' => $invoiceId)
                ));
            
        } else {
            $invoiceNumberInput = $this->generatingInvoiceNumber(1);
            $invoiceNumberOutput = $this->generatingInvoiceNumber(2);

            return $this->jsonResponse(array('invoice_number' => array('input' => $invoiceNumberInput, 'output' => $invoiceNumberOutput)));
        }

        return $this->jsonResponse(array('grid_data' => $arrInvoiceDetail, 'form_data' => $entityInvoice));
    }

    /**
     * @Route("/Input_Update", name="Input_Update")
     */
    public function Input_UpdateAction()
    {
        $entityService = $this->getEntityService();

        $params        =  $this->get('request')->getContent();
        $params = json_decode($params);
        $formParent = (array)$params->form_fields_value;
        $form_fields_value = (array)$formParent[0];
        $grid_value = (array)$params->grid_value;

        if ($form_fields_value['invoiceNumber'] != "") {
            $arrCheckInvoice = $this->getEntityService()->getAllData(
                'Invoice',
                array(
                    'conditions' => array('invoiceNumber' => $form_fields_value['invoiceNumber'])
                ));

            if ($arrCheckInvoice) {
                $invoiceId = $arrCheckInvoice[0]['id'];
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
                    array('update' => $form_fields_value,
                        'conditions' => array('id' => $invoiceId)
                    )
                );
            }

        } else {
            //Insert
            unset($form_fields_value['id']);
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
            $arrNewId = array();

            foreach ($grid_value as $rowValue) {
                $arrData = (array)$rowValue;

                if ($arrData['id'] == 0) {
                    unset($arrData['id']);
                    $arrData['invoiceId'] = $invoiceId;
                    $arrInsert[] = $arrData;
                } else {
                    $arrNewId[] = $arrData['id'];
                }

                if (count($arrInvoiceDetail) > 0 && $arrData['id']) {

                    for ($i = 0; $i < count($arrInvoiceDetail); $i++){
                        if ($arrInvoiceDetail[$i]['id'] == $arrData['id']) {
                            $arrData['invoiceId'] = $invoiceId;
                            $arrUpdate[] = $arrData;
                        }
                    }

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
                        array('update' => $arrEntity0,
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
                $entityService->delete('InvoiceDetail', $arrDelete);
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

    //PHIEU XUAT
    /**
     * @Route("/Output_Load", name="Output_Load")
     */
    public function Output_LoadAction()
    {
        //Phiếu Xuất: type= 2
        $arrData = array();
        $entityInvoice = array();

        if (!isset($arrGet['id'])) {
//            $invoiceId = $arrGet['id'];
            $invoiceId = 2;

            $entityInvoice = $this->getEntityService()->getAllData(
                'Invoice',
                array(
                    'orderBy'     => array('id' => 'DESC'),
                    'conditions'  => array('id' => $invoiceId)
                ))[0];

            $arrInvoiceDetail = $this->getEntityService()->getAllData(
                'InvoiceDetail',
                array(
                    'orderBy'    => array('id' => 'DESC'),
                    'conditions' => array('invoiceId' => $invoiceId)
                ));

            foreach($arrInvoiceDetail as $entity){
                $arrData[] = $entity;
            }
        }

        return $this->jsonResponse(array('grid_data' => $arrData, 'form_data' => $entityInvoice));
    }

    /**
     * @Route("/Output_Update", name="Output_Update")
     */
    public function Output_UpdateAction()
    {
        $entityService = $this->getEntityService();

        $params        =  $this->get('request')->getContent();
        $params = json_decode($params);
        $form_fields_value = (array)$params->form_fields_value;
        $grid_value = (array)$params->grid_value;

        if ($form_fields_value['id'] != "") {
            //Update

            $invoiceId = $form_fields_value['id'];
            unset($form_fields_value['id']);

            $entityService->dqlUpdate(
                'Invoice',
                array('update' => $form_fields_value,
                    'conditions' => array('id' => $invoiceId)
                )
            );

        } else {
            //Insert
            unset($form_fields_value['id']);
            $invoiceId = $entityService->rawSqlInsert('Invoice', array('insert' => $form_fields_value));
        }

        //Update Invoice Detail
        $arrInvoiceDetail = $this->getEntityService()->getAllData(
            'InvoiceDetail',
            array(
                'orderBy'    => array('id' => 'DESC'),
                'conditions' => array('invoiceId' => $invoiceId = 2)
            ));

        if (count($grid_value) > 0) {
            $arrInsert = array();
            $arrUpdate = array();
            $arrDelete = array();

            foreach ($grid_value as $rowValue) {
                $arrData = (array)$rowValue;
                $arrData['invoiceType'] = 2;

                if ($arrData['id'] == 0) {
                    $arrInsert[] = $arrData;
                }

                if (count($arrInvoiceDetail) > 0 && $arrData['id']) {

                    for ($i = 0; $i < count($arrInvoiceDetail); $i++){
                        if ($arrInvoiceDetail[$i]['id'] == $arrData['id']) {
                            $arrUpdate['id'] = $arrData;
                        } else {
                            $arrDelete['id'] = $arrInvoiceDetail[$i]['id'];
                        }
                    }

                }
            }

            //Update
            $entityService->dqlUpdate(
                'InvoiceDetail',
                array('update' => $arrUpdate,
                    'conditions' => array('id' => $id = 0)
                )
            );

            //Insert
            $invoiceId = $entityService->rawSqlInsert('InvoiceDetail', array('insert' => $arrInsert));

            //Delete
            $entityService->delete('InvoiceDetail', $arrDelete);
        }

        return $this->jsonResponse(array('data' => array()));
    }

    /**
     * @Route("/Output_Delete", name="Output_Delete")
     */
    public function Output_DeleteAction()
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
        $dateCurrent = (new \DateTime())->format('Ymd');

        if ($invoiceType == 1) {
            $invoiceNumber = 'PN';
        } else {
            $invoiceNumber = 'PX';
        }

        $entityInvoice = $this->getEntityService()->getAllData(
            'Invoice',
            array(
                'orderBy'     => array('id' => 'DESC'),
                'conditions'  => array('invoiceType' => $invoiceType)
            ))[0];

        if (count($entityInvoice) > 0) {
            $oldInvoiceNumber = $entityInvoice['invoiceNumber'];
            $arrTemp = explode('/', $oldInvoiceNumber);
            $newNum = (int)$arrTemp[2] + 1;

            $invoiceNumberNew = $invoiceNumber.'/'.$dateCurrent.'/'.$newNum;
        } else {
            $invoiceNumberNew = $invoiceNumber.'/'.$dateCurrent.'/1';
        }

        return $invoiceNumberNew;

    }
}