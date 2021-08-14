<?php

namespace Mycostum\IntegraCommerce\StoreConfig;

class LogConfig extends AbstractConfig
{
    
    /** @var string */
    protected $group = 'log';
    
    
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->getIntegraCommerceModuleConfig('enabled');
    }
    
    
    /**
     * @return string
     */
    public function getFilename()
    {
        return (string) $this->getIntegraCommerceModuleConfig('filename');
    }
}
