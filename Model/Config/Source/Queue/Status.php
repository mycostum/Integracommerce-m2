<?php

namespace Mycostum\IntegraCommerce\Model\Config\Source\Queue;

use Mycostum\IntegraCommerce\Model\Config\Source\AbstractSource;
use Mycostum\IntegraCommerce\Model\Queue;

class Status extends AbstractSource
{
    
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Queue::STATUS_PENDING => __('Pending'),
            Queue::STATUS_FAIL    => __('Fail'),
            Queue::STATUS_RETRY   => __('Retry'),
        ];
    }
}
