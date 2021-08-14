<?php

namespace Mycostum\IntegraCommerce\Model\Config\Source\Queue;

use Mycostum\IntegraCommerce\Model\Config\Source\AbstractSource;
use Mycostum\IntegraCommerce\Model\Queue;

class ProcessType extends AbstractSource
{
    
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Queue::PROCESS_TYPE_IMPORT => __('Import Process'),
            Queue::PROCESS_TYPE_EXPORT => __('Export Process'),
        ];
    }
}
