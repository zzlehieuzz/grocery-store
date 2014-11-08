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

        if ($id = 1) {
            $arrEntity = $this->getEntityService()->getAllData(
                'Invoice',
                array('orderBy' => array('id' => 'DESC')));

            foreach($arrEntity as $entity){
                unset($entity['createInvoiceDate']);
                $arrData[] = $entity;
            }
        }

        return $this->jsonResponse(array('data' => $arrData));
    }

    /**
     * @Route("/Input_Update", name="Input_Update")
     */
    public function Input_UpdateAction()
    {
//        $params        = $this->getJsonParams();
        $params        =  $this->get('request')->getContent();
        $params = json_decode($params);
        $form_fields_value = $params->form_fields_value;
        $grid_value = $params->grid_value;

//        echo "<pre>";
//        print_r($form_fields_value[0]);
//        print_r($grid_value); die;

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
                array('orderBy' => array('id' => 'DESC')));

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