<?php

namespace Sof\ApiBundle\Entity;

use Sof\ApiBundle\Entity\ValueConst\InvoiceConst;
use Sof\ApiBundle\Lib\SofUtil;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends BaseRepository
{

    /**
     * @param $fromDate
     * @param $toDate
     * @return Array
     *
     * @author HieuNLD 2014/11/21
     */
    public function getData_ReportInventory($fromDate, $toDate) {

        $invoiceDetail = self::ENTITY_BUNDLE . ":InvoiceDetail";
        $invoice       = self::ENTITY_BUNDLE . ":Invoice";

        $query = $this->querySimpleEntities(array(
            'selects' => array('id', 'name AS productName'),
            'conditions' => array(
//                'invoiceType'   => InvoiceConst::INVOICE_TYPE_2,
//                'paymentStatus' => InvoiceConst::PAYMENT_STATUS_1
            )
        ));
        $query->addSelect('SELECT SUM(iInputDetail.quantity) FORM ' . $invoice . ' AS iInput '
            . ' LEFT JOIN ' . $invoiceDetail . ' AS iInputDetail ON iInput.invoiceType = 1'
            . ' WHERE iInputDetail.productId = entity.id AND iInput.invoiceType = 1 AS totalInput');
        $query->addSelect('SELECT SUM(iOutputDetail.quantity) FORM ' . $invoice . ' AS iOutput '
            . ' LEFT JOIN ' . $invoiceDetail . ' AS iOutputDetail ON iOutput.invoiceType = 2'
            . ' WHERE iOutputDetail.productId = entity.id AND iOutput.invoiceType = 2 AS totalOutput');

        $query->groupBy('entity.id');
        $query->groupBy('iInputDetail.productId');
        $query->groupBy('iOutputDetail.productId');


        print_r(SofUtil::formatScalarArray($query->getQuery()->getScalarResult()));
        die;
        return SofUtil::formatScalarArray($query->getQuery()->getScalarResult());
    }
}
