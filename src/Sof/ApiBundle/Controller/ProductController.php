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
     * @Route("/Product_LoadAll", name="Product_LoadAll")
     */
    public function Product_LoadAllAction()
    {
        $arrEntity = $this->getEntityService()->getAllData('Product', array('orderBy' => array('id' => 'DESC')));

        return $this->jsonResponse(array('data' => $arrEntity));
    }

    /**
     * @Route("/Product_Update", name="Product_Update")
     */
    public function Product_UpdateAction()
    {
        $params        = $this->getJsonParams();
        $entityService = $this->getEntityService();
        $arrProduct    = $arrProductUnit = array();

        $productId     = $params['id'];
        $productUnitId = $params['productUnitId'];
        //Product
        $arrProduct['code']          =  $params['code'];
        $arrProduct['name']          =  $params['name'];
        $arrProduct['originalPrice'] =  $params['originalPrice'];
        $arrProduct['salePrice']     =  $params['salePrice'];
        //ProductUnit
        $arrProductUnit['unitId1']       = $params['unitId1'];
        $arrProductUnit['unitId2']       = $params['unitId2'];
        $arrProductUnit['convertAmount'] = $params['convertAmount'];
        $arrProductUnit['description']   = '';

        //Update
        if ($productId != 0 && $productUnitId != 0) {
            $entityService->dqlUpdate(
                'Product',
                array('update' => $arrProduct,
                  'conditions' => array('id' => $productId)
                )
            );

            $entityService->dqlUpdate(
              'ProductUnit',
              array('update' => $arrProductUnit,
                'conditions' => array('id' => $productUnitId)
              )
            );
        }
        //Insert
        else {
          $newProductId = $entityService->rawSqlInsert('Product', array('insert' => $arrProduct));
          $arrProductUnit['productId'] = $newProductId;

          $newProductUnitId = $entityService->rawSqlInsert('ProductUnit', array('insert' => $arrProductUnit));
          $entityService->dqlUpdate(
            'Product',
            array('update' => array('productUnitId' => $newProductUnitId),
              'conditions' => array('id' => $newProductId)
            )
          );
        }

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