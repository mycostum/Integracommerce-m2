<?php

namespace Mycostum\IntegraCommerce\Block\Adminhtml\Sales\Order\View;

use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;

class IntegracommerceInfo extends AbstractOrder
{
    
    /**
     * @return null|string
     */
    public function getIntegracommerceCode()
    {
        if (!$this->canDisplay()) {
            return null;
        }
        
        return $this->getIntegracommerceInfo()->getCode();
    }

    /**
     * @return null|string
     */
    public function getIntegracommerceChannel()
    {
        if (!$this->canDisplay()) {
            return null;
        }
    
        return $this->getIntegracommerceInfo()->getChannel();
    }

    /**
     * @return \Mycostum\IntegraCommerce\Api\Data\OrderInterface|null
     */
    public function getIntegracommerceInfo()
    {
        try {
            return $this->getOrder()->getExtensionAttributes()->getIntegracommerceInfo();
        } catch (\Exception $e) {
        }
        
        return null;
    }

    /**
     * @return bool
     */
    public function canDisplay()
    {
        try {
            if (!$this->getOrder() || !$this->getOrder()->getEntityId()) {
                return false;
            }
            
            if (!$this->getOrder()->getExtensionAttributes()) {
                return false;
            }
            
            if (!$this->getOrder()->getExtensionAttributes()->getIntegracommerceInfo()) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        
        return true;
    }
}
