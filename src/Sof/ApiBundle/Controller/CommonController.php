<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Lib\DateUtil;

class CommonController extends BaseController
{
    public function preAction()
    {
        $this->getApiParams();
    }

    /**
     * @Route("/Common_Index", name="Common_Index")
     * @Method("GET")
     * @Template("SofApiBundle:Common:index.html.twig")
     */
    public function Common_IndexAction()
    {
        $a = array(
            'name' => 'hieu',
        );

        return array('name' => 10);
//        return $this->apiResponse(array());
    }

    /**
     * @Route("/Common_LoadPlayer", name="Common_LoadPlayer")
     */
    public function Common_LoadPlayerAction()
    {
        $arrEntity = $this->getEntityService()->getAllData('User', array('orderBy' => array('id' => 'DESC')));

        return $this->getJsonResponse(array('data' => $arrEntity));
    }
}