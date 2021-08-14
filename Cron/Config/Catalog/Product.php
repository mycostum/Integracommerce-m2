<?php

namespace Mycostum\IntegraCommerce\Cron\Config\Catalog;

use Mycostum\IntegraCommerce\Cron\Config\AbstractCronConfig;

class Product extends AbstractCronConfig
{
    
    /** @var string */
    protected $group = 'cron_catalog_product';

    /**
     * @var $scopeCode
     *
     * @return integer
     */
    public function getQueueCreateLimit($scopeCode = null)
    {
        return (int) $this->getGroupConfig('queue_create_limit', $scopeCode);
    }

    /**
     * @var $scopeCode
     *
     * @return integer
     */
    public function getQueueExecuteLimit($scopeCode = null)
    {
        return (int) $this->getGroupConfig('queue_execute_limit', $scopeCode);
    }
}
