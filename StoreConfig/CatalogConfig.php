<?php

namespace Mycostum\IntegraCommerce\StoreConfig;

class CatalogConfig extends AbstractConfig
{
    
    /** @var string */
    protected $group = 'catalog';


    /**
     * @return bool
     */
    public function hasActiveIntegrateOnSaveFlag()
    {
        return (bool) $this->getIntegraCommerceModuleConfig('immediately_integrate_product_after_sensitive_change');
    }
    
    
    /**
     * @return array
     */
    public function getProductVisibilities()
    {
        return (array) $this->getIntegraCommerceModuleConfigAsArray('product_visibility');
    }
    
    
    /**
     * @param int $visibility
     *
     * @return bool
     */
    public function hasAllowedVisibility($visibility)
    {
        if (in_array($visibility, $this->getProductVisibilities())) {
            return true;
        }
        
        return false;
    }
}
