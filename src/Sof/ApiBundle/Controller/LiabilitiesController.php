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
        $entityService = $this->getEntityService();
//        $arrEntity = $entityService->selectOnDefault('Liabilities:getData', array('customerId' => 1));
//
//        print_r($arrEntity);
//        die;


        $arr = array();

        for($i=0;$i<21;$i++) {
            $arr[$i]['id'] = $i;
            $arr[$i]['name'] = 'Ext Forms ' . $i;
            $arr[$i]['amount'] = 15;
            $arr[$i]['price'] = 20;

            if (in_array($i, array(1, 2,3))) {
                $arr[$i]['invoiceId'] = 1;
                $arr[$i]['invoiceNumber'] = 'invoice number 1';
            }
            if (in_array($i, array(0, 4, 5,6,7,8,9))) {
                $arr[$i]['invoiceId'] = 2;
                $arr[$i]['invoiceNumber'] = 'invoice number 2';
            }
            if (in_array($i, array(10, 11,12, 13))) {
                $arr[$i]['invoiceId'] = 3;
                $arr[$i]['invoiceNumber'] = 'invoice number 3';
            }
            if (in_array($i, array(14, 15,16,17,18,19,20))) {
                $arr[$i]['invoiceId'] = 4;
                $arr[$i]['invoiceNumber'] = 'invoice number 4';
            }
        }

        return $this->jsonResponse(array('data' => $arr), 21);
    }

    /**
     * @Route("/Liabilities_Customer_Load", name="Liabilities_Customer_Load")
     */
    public function Liabilities_Customer_LoadAction()
    {
        $arr = array();
        for($i=0;$i<21;$i++) {
            $arr[$i]['id'] = $i;
            $arr[$i]['name'] = 'Customer ' . $i;
        }
        return $this->jsonResponse(array('data' => $arr), 15);
    }
}