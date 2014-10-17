<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Lib\DateUtil;
use Sof\ApiBundle\Entity\Driver;

class CommonController extends AppController
{
    public function preAction()
    {
//        $this->getApiParams();
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
    }

    /**
     * @Route("/Common_LoadPlayer", name="Common_LoadPlayer")
     */
    public function Common_LoadPlayerAction()
    {
//        $arrEntity = $this->getEntityService()->getAllData('User', array('orderBy' => array('id' => 'DESC')));
      $arrEntity0 = $this->getDoctrine()
                  ->getRepository('SofApiBundle:User')->find(1);

        return $this->getJsonResponse(array('data' => $this->objToArray(array($arrEntity0))));
    }

  /**
   * @Route("/Common_LoadDriver", name="Common_LoadDriver")
   */
  public function Common_LoadDriverAction()
  {
//        $arrEntity = $this->getEntityService()->getAllData('User', array('orderBy' => array('id' => 'DESC')));
//    $arrEntity = $this->getEntity('Driver', array('id' => 1));
    $arrEntity = $this->get('entity_service')->process('Driver:findById', array(1,3,4));

    return $this->getJsonResponse(array('data' => $this->objToArray($arrEntity)));
  }

  /**
   * @Route("/updateData", name="updateData")
   */
  public function updateDataAction()
  {
    $params = array();
    $request = $this->get('request');

    $params['id']       = $request->get('id');
    $params['name']     = $request->get('name');
    $params['userName'] = $request->get('userName');
    $params['email']    = $request->get('email');

    if ($params['id'] != 0) {
      $entity = $this->getEntity('Driver', array('id' => $params['id']));
    } else {
      $entity = new Driver();
    }

    $entity->setUserName($params['userName']);
    $entity->setName($params['name']);
    $entity->setEmail($params['email']);

    $this->get('entity_service')->save($entity);

    return $this->getJsonResponse(array('data' => 1));
  }
  /**
   * @Route("/deleteData", name="deleteData")
   */
  public function deleteDataAction()
  {
    $params = array();
    $request = $this->get('request');
    $params['id'] = $request->get('id');

    if ($params['id'] != 0) {
      $this->delete('Driver', (int)$params['id']);
    }

    return $this->getJsonResponse(array('data' => 1));
  }

  private function objToArray($arrEntityL){
    $arrRes = array();

    foreach($arrEntityL as $key=>$arrEntity) {
      $arrRes[$key]['id'] = $arrEntity->getId();
      $arrRes[$key]['role_id'] = $arrEntity->getRoleId();
      $arrRes[$key]['userName'] = $arrEntity->getUserName();
      $arrRes[$key]['password'] = $arrEntity->getPassword();
      $arrRes[$key]['name'] = $arrEntity->getName();
      $arrRes[$key]['email'] = $arrEntity->getEmail();
    }

    return $arrRes;
  }
}