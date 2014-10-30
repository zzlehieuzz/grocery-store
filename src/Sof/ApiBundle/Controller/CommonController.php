<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\ValueConst\BaseConst;

class CommonController extends BaseController
{
    /**
     * @Route("/Common_Index", name="Common_Index")
     * @Method("GET")
     * @Template("SofApiBundle:Common:index.html.twig")
     */
    public function Common_IndexAction()
    {
        $module = $this->getEntityService()->getAllData('Module',
            array('selects' => array('name', 'iconCls', 'module'),
                  'conditions' => array('isActive' => BaseConst::FLAG_ON),
                  'orderBy' => array('sort')));

        $user = $this->get('security.context')->getToken()->getUser();

        $userData = $this->getEntityService()->getFirstData('User',
            array('selects' => array('id', 'roleId', 'userName', 'password', 'name'),
                  'conditions' => array('userName' => $user->getUserName())));

        return array('moduleJson' => json_encode($module),
                     'userJson'   => json_encode($userData));
    }
}