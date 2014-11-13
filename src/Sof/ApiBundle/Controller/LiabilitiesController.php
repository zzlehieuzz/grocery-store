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
}