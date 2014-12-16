<?php
namespace Sof\ApiBundle\Entity\ValueConst;

class InvoiceConst extends BaseConst
{
    const INVOICE_TYPE_1 = 1;//Phiếu Nhập
    const INVOICE_TYPE_2 = 2;//Phiếu Xuất

    const PAYMENT_STATUS_1 = 1;//Chưa giao hàng
    const PAYMENT_STATUS_2 = 2;//Đang giao hàng
    const PAYMENT_STATUS_3 = 3;//Đã giao hàng
}