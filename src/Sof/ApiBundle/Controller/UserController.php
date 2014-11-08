<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\ValueConst\BaseConst;

class UserController extends BaseController
{

    /**
     * @Route("/User_Load", name="User_Load")
     */
    public function User_LoadAction()
    {
        $params = $this->getPagingParams();

        $arrEntity = $this->getEntityService()->getDataForPaging('User',
            array('conditions' => array('roleId' => array('<>' => BaseConst::FLAG_OFF)),
                  'orderBy' => array('id' => 'DESC'),
                  'firstResult' => $params['start'],
                  'maxResults' => $params['limit']
            ));

        return $this->jsonResponse(array('data' => $arrEntity['data']), $arrEntity['total']);
    }

    /**
     * @Route("/User_Update", name="User_Update")
     */
    public function User_UpdateAction()
    {
      $params        = $this->getJsonParams();
      $entityService = $this->getEntityService();
      if ($params['id']) {
          $entityService->dqlUpdate(
              'User',
              array('update' => $params,
                  'conditions' => array('id' => $params['id'])
              )
          );
      } else {
          $params['roleId']   = 1;
          $params['password'] = md5(123456);
          $id = $entityService->rawSqlInsert('User', array('insert' => $params));
          $params['id'] = $id;
      }
      $entityService->completeTransaction();

      return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/User_Delete", name="User_Delete")
     */
    public function User_DeleteAction()
    {
        $entityService = $this->getEntityService();

        $params = $this->getJsonParams();

        $entityService->dqlDelete(
            'User',
            array(
                'conditions' => array(
                    'id'   => $params,
                )
            )
        );
        $entityService->completeTransaction();

        return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/User_ChangePassword", name="User_ChangePassword")
     */
    public function User_ChangePasswordAction()
    {
        $params        = $this->getJsonParams();
        $entityService = $this->getEntityService();

        if ($params['id'] && $params['newPass']) {
            $entityService->dqlUpdate(
                'User',
                array('update' => array('password' => md5($params['newPass'])),
                      'conditions' => array('id' => $params['id'])
                )
            );
        } else {
            $entityService->rawSqlInsert('User', array('insert' => $params));
        }

        $entityService->completeTransaction();

        return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/User_ChangeProfile", name="User_ChangeProfile")
     */
    public function User_ChangeProfileAction()
    {
        $params        = $this->getJsonParams();
        $entityService = $this->getEntityService();

        $data = array();
        if (isset($params['profileNewPass']) && ($password = $params['profileNewPass'])) {
            $data['password'] = md5($password);
        }

        if (isset($params['profileName']) && ($name = $params['profileName'])) {
            $data['name'] = $name;
        }
        $newData = array();
        if ($data) {
            $entityService->dqlUpdate('User', array('update' => $data, 'conditions' => array('id' => $params['userId'])));
            $entityService->completeTransaction();
            $newData = $entityService->getAllData('User',
                array('selects' => array('id', 'roleId', 'userName', 'password', 'name'),
                      'conditions' => array('id' => $params['userId'])));
        }

        return $this->jsonResponse(array('data' => $newData));
    }
}