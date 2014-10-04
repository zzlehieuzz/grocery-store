<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class ManagementNoticeInformationConst extends BaseEntity
{
    //notice_section: 告知区分
    const NOTICE_SECTION_MAINTENANCE  = 1;//メンテナンス
    const NOTICE_SECTION_OPERATION    = 2;//運営からのお知らせ
}
