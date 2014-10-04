<?php

namespace Sof\ApiBundle\Controller\CommonFunc;

use Sof\ApiBundle\Service\EntityService;

trait BaseFunc
{
    /**
     * @return EntityService
     */
    abstract public function getEntityService();
}
