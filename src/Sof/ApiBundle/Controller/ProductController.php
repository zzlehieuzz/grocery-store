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
                  'firstResult' => $params['start'],
                  'maxResults' => $params['limit']
            ));

        $arrTemp = array();
        foreach($arrEntity['data'] as $key=>$entity) {
            $productUnitId = (int)$entity['productUnitId'];

            if ($productUnitId != 0) {
                $arrEntity0 = $this->getEntityService()->getAllData('ProductUnit', array('conditions' => array('id' => $productUnitId)));

                if (isset($arrEntity0[0]) && count($arrEntity0) > 0) {
                    $arrTemp[$key]['unitId1'] = $arrEntity0[0]['unitId1'];
                    $arrTemp[$key]['unitId2'] = $arrEntity0[0]['unitId2'];;
                    $arrTemp[$key]['convertAmount'] = $arrEntity0[0]['convertAmount'];
                }
            } else {
                $arrTemp[$key]['unitId1'] = null;
                $arrTemp[$key]['unitId2'] = null;
                $arrTemp[$key]['convertAmount'] = null;
            }

            $arrTemp[$key]['id']            = $entity['id'];
            $arrTemp[$key]['productUnitId'] = $productUnitId;
            $arrTemp[$key]['name']          = $entity['name'];
            $arrTemp[$key]['code']          = $entity['code'];
            $arrTemp[$key]['originalPrice'] = $entity['originalPrice'];
            $arrTemp[$key]['salePrice']     = $entity['salePrice'];
        }

        return $this->jsonResponse(array('data' => $arrTemp), $arrEntity['total']);
    }

    /**
     * @Route("/Product_Update", name="Product_Update")
     */
    public function Product_UpdateAction()
    {
      $params        = $this->getJsonParams();
      $entityService = $this->getEntityService();
      $arrProduct = array();
      $arrProduct['id']   =  $params['id'];
      $arrProduct['code'] =  $params['code'];
      $arrProduct['name'] =  $params['name'];
      $arrProduct['originalPrice'] =  $params['originalPrice'];
      $arrProduct['salePrice']     =  $params['salePrice'];

      if ($params['id'] != 0) {
        $entityService->dqlUpdate(
          'Product',
          array('update' => $arrProduct,
            'conditions' => array('id' => $params['id'])
          )
        );

        $productId = $params['id'];

      } else {
        $lastId = $entityService->rawSqlInsert('Product', array('insert' => $arrProduct));
        $productId = $lastId;
      }

        $productUnitId = $params['productUnitId'];
        $params['productId'] = $params['id'];

        unset($params['id']);
        unset($params['code']);
        unset($params['name']);
        unset($params['productUnitId']);
        unset($params['originalPrice']);
        unset($params['salePrice']);

        if ((int)$productUnitId != 0) {
            $entityService->dqlUpdate(
                'ProductUnit',
                array('update' => $params,
                    'conditions' => array('id' => $productUnitId)
                )
            );
        } else {
            $lastId = $entityService->rawSqlInsert('ProductUnit', array('insert' => $params));
            $productUnitId = $lastId;
        }

        $entityService->dqlUpdate(
            'Product',
            array('update' => array('productUnitId' => $productUnitId),
                    'conditions' => array('id' => $productId)
            )
        );

        $entityService->completeTransaction();

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