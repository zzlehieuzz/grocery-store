<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\Product;
use Sof\ApiBundle\Lib\DateUtil;

class ReportController extends BaseController
{

    /**
     * @Route("/Report_InventoryLoad", name="Report_InventoryLoad")
     */
    public function Report_InventoryLoadAction()
    {
        $params = $this->getPagingParams();

        $fromDate = $this->getRequestData()->get('fromDate');
        $toDate   = $this->getRequestData()->get('toDate');

        $entityService = $this->getEntityService();
        $arrEntity = $this->getEntityService()->getDataForPaging('Product',
            array('orderBy' => array('id' => 'DESC'),
                  'firstResult' => $params['start'],
                  'maxResults' => $params['limit']
            ));


        $arrCustomer = $entityService->selectOnDefault('Product:getData_ReportInventory', $fromDate, $toDate);


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
}