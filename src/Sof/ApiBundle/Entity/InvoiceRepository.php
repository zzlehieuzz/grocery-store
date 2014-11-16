<?php

namespace Sof\ApiBundle\Entity;

use Sof\ApiBundle\Entity\ValueConst\InvoiceConst;
use Sof\ApiBundle\Lib\SofUtil;

/**
 * InvoiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceRepository extends BaseRepository
{
    /**
     * @return Array
     *
     * @author HieuNLD 2014/11/13
     */
    public function getData($id, $searchInvoiceName) {
        $query = $this->querySimpleEntities(array(
            'selects' => array('id AS invoiceId', 'invoiceNumber'),
            'conditions' => array('subject'       => $id,
                                  'invoiceType'   => InvoiceConst::INVOICE_TYPE_2,
                                  'paymentStatus' => InvoiceConst::PAYMENT_STATUS_1
            )
        ));
        $query->addSelect('l.id, l.name, l.amount, l.price, l.customerId');
        $query->leftJoin(self::ENTITY_BUNDLE . ":Liabilities", 'l', 'WITH', "entity.subject = l.customerId AND entity.id = l.invoiceId");

        if ($searchInvoiceName) {
            $query->andWhere('entity.invoiceNumber LIKE :invoiceNumber')
                  ->setParameter('invoiceNumber', '%'.$searchInvoiceName.'%');
        }

        return SofUtil::formatScalarArray($query->getQuery()->getScalarResult());
    }
}
