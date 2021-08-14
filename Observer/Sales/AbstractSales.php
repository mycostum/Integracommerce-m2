<?php

namespace Mycostum\IntegraCommerce\Observer\Sales;

use Mycostum\IntegraCommerce\Observer\AbstractObserver;

abstract class AbstractSales extends AbstractObserver
{
    
    /**
     * @param string $configStatus
     * @param string $currentStatus
     *
     * @return bool
     */
    protected function statusMatches($configStatus, $currentStatus)
    {
        if ($currentStatus == $configStatus) {
            return true;
        }
        
        return false;
    }
}
