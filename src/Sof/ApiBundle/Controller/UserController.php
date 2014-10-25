<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\User;
use Sof\ApiBundle\Lib\DateUtil;

class UserController extends BaseController
{

    /**
     * @Route("/User_Load", name="User_Load")
     */
    public function User_LoadAction()
    {
        $params = $this->getPagingParams();

        $arrEntity = $this->getEntityService()->getDataForPaging('User',
            array('orderBy' => array('id' => 'DESC'),
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

      if ($params['id'] != 0) {
        $entityService->dqlUpdate(
          'User',
          array('update' => $params,
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
}