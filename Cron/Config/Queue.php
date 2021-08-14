<?php

namespace Mycostum\IntegraCommerce\Cron\Config;

class Queue extends AbstractCronConfig
{
    
    /** @var string */
    protected $group = 'cron_queue_clean';
}
