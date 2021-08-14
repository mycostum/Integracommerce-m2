<?php

namespace Mycostum\IntegraCommerce\Cron\Config\Sales\Order;

use Mycostum\IntegraCommerce\Cron\Config\AbstractCronConfig;

class Status extends AbstractCronConfig
{
    
    protected $group = 'cron_sales_order_status';
    
    /**
     * @var $scopeCode
     *
     * @return int
     */
    public function queueCreateLimit($scopeCode = null)
    {
        return (int) $this->getGroupConfig('queue_create_limit', $scopeCode);
    }
    
    /**
     * @var $scopeCode
     *
     * @return int
     */
    public function queueExecuteLimit($scopeCode = null)
    {
        return (int) $this->getGroupConfig('queue_execute_limit', $scopeCode);
    }
}
