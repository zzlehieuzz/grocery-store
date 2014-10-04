<?php

namespace Sof\ApiBundle\Service\CommonFunc;

use Sof\ApiBundle\Service\EntityService;

trait BaseFunc
{
    /**
     * @return EntityService
     */
    abstract public function getEntityService();
}
