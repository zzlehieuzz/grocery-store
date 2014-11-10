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
        $arrEntity = $this->getEntityService()->getAllData(
            'Invoice',
            array('orderBy' => array('id' => 'DESC')));

        $arrData = array();
        foreach($arrEntity as $entity){
            unset($entity['createInvoiceDate']);
            $arrData[] = $entity;
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

        $arrData = array();
        $entityInvoice = array();

        if ($id = 1) {
            $entityInvoice = $this->getEntityService()->getAllData(
                'Invoice',
                array(
                      'orderBy'     => array('id' => 'DESC'),
                      'conditions'  => array('id' => 1)
                ))[0];

            $arrInvoiceDetail = $this->getEntityService()->getAllData(
                'InvoiceDetail',
                array(
                    'orderBy'    => array('id' => 'DESC'),
                    'conditions' => array('invoiceId' => 1)
                ));

            foreach($arrInvoiceDetail as $entity){
//                unset($entity['createInvoiceDate']);
                $arrData[] = $entity;
            }

//            echo '<pre>';
//            var_dump($arrInvoiceDetail); die;
//            var_dump($entityInvoice[0]); die;
        }

        return $this->jsonResponse(array('grid_data' => $arrData, 'form_data' => $entityInvoice));
    }

    /**
     * @Route("/Input_Update", name="Input_Update")
     */
    public function Input_UpdateAction()
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
                'conditions' => array('invoiceId' => $invoiceId = 1)
            ));

        if (count($grid_value) > 0) {
            $arrInsert = array();
            $arrUpdate = array();
            $arrDelete = array();

            foreach ($grid_value as $rowValue) {
                $arrData = (array)$rowValue;
                $arrData['invoiceType'] = 1;

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
     * @Route("/Input_Delete", name="Input_Delete")
     */
    public function Input_DeleteAction()
    {
        $arrEntity = $this->getEntityService()->getAllData(
            'Invoice',
            array('orderBy' => array('id' => 'DESC')));

        $arrData = array();
        foreach($arrEntity as $entity){
            unset($entity['createInvoiceDate']);
            $arrData[] = $entity;
        }

        return $this->jsonResponse(array('data' => $arrData));
    }

    //PHIEU XUAT
    /**
     * @Route("/Output_Load", name="Output_Load")
     */
    public function Output_LoadAction()
    {
        //Phiếu Xuất: type= 2
        $arrData = array();

        if ($id = 1) {
            $arrEntity = $this->getEntityService()->getAllData(
                'Invoice',
                array(
                    'orderBy' => array('id' => 'DESC'),
                    'conditions' => array('id' => 2)
                ));

            foreach($arrEntity as $entity){
                unset($entity['createInvoiceDate']);
                $arrData[] = $entity;
            }
        }

        return $this->jsonResponse(array('data' => $arrData));
    }

    /**
     * @Route("/Output_Update", name="Output_Update")
     */
    public function Output_UpdateAction()
    {
        $arrEntity = $this->getEntityService()->getAllData(
            'Invoice',
            array('orderBy' => array('id' => 'DESC')));

        $arrData = array();
        foreach($arrEntity as $entity){
            unset($entity['createInvoiceDate']);
            $arrData[] = $entity;
        }

        return $this->jsonResponse(array('data' => $arrData));
    }

    /**
     * @Route("/Output_Delete", name="Output_Delete")
     */
    public function Output_DeleteAction()
    {
        $arrEntity = $this->getEntityService()->getAllData(
            'Invoice',
            array('orderBy' => array('id' => 'DESC')));

        $arrData = array();
        foreach($arrEntity as $entity){
            unset($entity['createInvoiceDate']);
            $arrData[] = $entity;
        }

        return $this->jsonResponse(array('data' => $arrData));
    }
}