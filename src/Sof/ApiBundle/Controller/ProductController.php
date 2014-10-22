<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\Product;
use Sof\ApiBundle\Lib\DateUtil;

class ProductController extends BaseController
{

    /**
     * @Route("/Product_Load", name="Product_Load")
     */
    public function Product_LoadAction()
    {
        $params = $this->getPagingParams();

        $arrEntity = $this->getEntityService()->getDataForPaging('Product',
            array('orderBy' => array('id' => 'DESC'),
//                  'firstResult' => $params['start'],
//                  'maxResults' => $params['limit']
            ));

        return $this->jsonResponse(array('data' => $arrEntity['data']), $arrEntity['total']);
    }

    /**
     * @Route("/Product_Update", name="Product_Update")
     */
    public function Product_UpdateAction()
    {
      $params        = $this->getJsonParams();

      $entityService = $this->getEntityService();

      if ($params['id'] != 0) {
        $entityService->dqlUpdate(
          'Product',
          array('update' => $params,
            'conditions' => array('id' => $params['id'])
          )
        );
        $entityService->completeTransaction();
      } else {
        $entityService->rawSqlInsert('Product', array('insert' => $params));
      }

      return $this->jsonResponse(array('data' => $params));
    }

    /**
     * @Route("/Product_Delete", name="Product_Delete")
     */
    public function Product_DeleteAction()
    {
        $entityService = $this->getEntityService();

        $params = $this->getJsonParams();

        $entityService->dqlDelete(
            'Product',
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