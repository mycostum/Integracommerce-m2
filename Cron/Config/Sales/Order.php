<?php

namespace Mycostum\IntegraCommerce\Cron\Config\Sales;

use Mycostum\IntegraCommerce\Cron\Config\AbstractCronConfig;

class Order extends AbstractCronConfig
{
    
    /** @var string */
    protected $group = 'cron_sales_order_import';

    /**
     * @var $scopeCode
     *
     * @return int
     */
    public function getLimit($scopeCode = null)
    {
        return (int) $this->getGroupConfig('limit', $scopeCode);
    }
}
