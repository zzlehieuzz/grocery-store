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
        $arrEntity = $this->getEntityService()->getAllData(
            'Driver',
            array('orderBy' => array('id' => 'DESC')));

        return $this->jsonResponse(array('data' => $arrEntity));
    }

    /**
     * @Route("/Driver_Update", name="Driver_Update")
     */
    public function Driver_UpdateAction()
    {
        $entityService = $this->getEntityService();
        $params        = array();
        $request       = $this->get('request');

        $params['id']       = $request->get('id');
        $params['name']     = $request->get('name');
        $params['numberPlate'] = $request->get('numberPlate');

        if ($params['id'] != 0) {
            $entityService->dqlUpdate(
                'Driver',
                array('update' => $params,
                      'conditions' => array('id' => $params['id'])
                )
            );
            $entityService->completeTransaction();
        } else {
            $entityService->rawSqlInsert('Driver', array('insert' => $params));
        }

        return $this->jsonResponse(array('data' => 1));
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