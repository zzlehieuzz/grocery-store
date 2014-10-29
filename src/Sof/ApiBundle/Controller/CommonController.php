<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\ValueConst\BaseConst;
use Sof\ApiBundle\Lib\ArrayUtil;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        $encoder    = new JsonEncoder();
        $normalizer = new GetSetMethodNormalizer();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $moduleJson = $serializer->serialize($module, 'json');

        return array('moduleJson' => $moduleJson);
    }
}