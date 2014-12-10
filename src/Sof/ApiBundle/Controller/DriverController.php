<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\User;
use Sof\ApiBundle\Lib\DateUtil;

class DriverController extends BaseController
{

    /**
     * @Route("/Driver_Load", name="Driver_Load")
     */
    public function Driver_LoadAction()
    {
        $params = $this->getPagingParams();
        $arrEntity = $this->getEntityService()->getDataForPaging(
            'Driver',
            array('orderBy' => array('id' => 'DESC'),
                  'firstResult' => $params['start'],
                  'maxResults' => $params['limit']));

        return $this->jsonResponse(array('data' => $arrEntity['data']), $arrEntity['total']);
    }

    /**
     * @Route("/Driver_Update", name="Driver_Update")
     */
    public function Driver_UpdateAction()
    {
        $entityService = $this->getEntityService();
        $params        = $this->getJsonParams();
        $id            = $params['id'];
        unset($params['id']);
        if ($id) {
            $entityService->dqlUpdate(
                'Driver',
                array('update'     => $params,
                      'conditions' => array('id' => $id))
            );
        } else {
            $id = $entityService->rawSqlInsert('Driver', array('insert' => $params));
        }

        $params['id'] = $id;
        $entityService->completeTransaction();

        return $this->jsonResponse(array('data' => $params));
    }
    /**
     * @Route("/Driver_Delete", name="Driver_Delete")
     */
    public function Driver_DeleteAction()
    {
      $entityService = $this->getEntityService();
      $params = $this->getJsonParams();

      $entityService->dqlDelete(
        'Driver',
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