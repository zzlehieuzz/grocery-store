<?php
namespace Sof\ApiBundle\Entity\ValueConst;

class InvoiceConst extends BaseConst
{
    const INVOICE_TYPE_1 = 1;
    const INVOICE_TYPE_2 = 2;

    const INVOICE_TYPE_TEXT_1 = 'Phiếu Nhập';
    const INVOICE_TYPE_TEXT_2 = 'Phiếu Xuất';

    const PAYMENT_STATUS_1 = 1;
    const PAYMENT_STATUS_2 = 2;
    const PAYMENT_STATUS_3 = 3;
    const PAYMENT_STATUS_TEXT_1 = 'Chưa Thanh Toán';
    const PAYMENT_STATUS_TEXT_2 = 'Đang Thanh Toán';
    const PAYMENT_STATUS_TEXT_3 = 'Đã thanh Toán';

    const DELIVERY_STATUS_1 = 1;
    const DELIVERY_STATUS_2 = 2;

    const DELIVERY_STATUS_TEXT_1 = 'Chưa giao hàng';
    const DELIVERY_STATUS_TEXT_2 = 'Đã giao hàng';

}