<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Lib\DateUtil;

class LoginController extends BaseController
{
    public function preAction()
    {
        $this->getApiParams();
    }

    /**
     * @Route("/Login_Index", name="Login_Index")
     * @Method("GET")
     * @Template()
     */
    public function IndexAction()
    {
        $a = array(
            'name' => 'hieu',
        );

        return array('name' => 10);
//        return $this->apiResponse(array());
    }
}