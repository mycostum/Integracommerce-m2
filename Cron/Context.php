<?php

namespace Mycostum\IntegraCommerce\Cron;

use Mycostum\IntegraCommerce\Helper\Context as HelperContext;

class Context
{
    
    /** @var HelperContext */
    protected $helperContext;
    
    /** @var Config */
    protected $cronConfig;

    /**
     * Context constructor.
     *
     * @param HelperContext $helperContext
     * @param Config        $cronConfig
     */
    public function __construct(HelperContext $helperContext, Config $cronConfig)
    {
        $this->helperContext = $helperContext;
        $this->cronConfig    = $cronConfig;
    }
    
    /**
     * @return HelperContext
     */
    public function helperContext()
    {
        return $this->helperContext;
    }

    /**
     * @return Config
     */
    public function cronConfig()
    {
        return $this->cronConfig;
    }
}
