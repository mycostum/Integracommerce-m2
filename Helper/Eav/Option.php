<?php

namespace Mycostum\IntegraCommerce\Helper\Eav;

use Mycostum\IntegraCommerce\Helper\AbstractHelper;
use Mycostum\IntegraCommerce\Helper\Context;

class Option extends AbstractHelper
{
    
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }
    
    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param int                                 $optionId
     * @param null|\Magento\Store\Model\Store     $store
     *
     * @return mixed|null
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function extractAttributeOptionValue($attribute, $optionId, $store = null)
    {
        return $this->getEavAttributeOptionResource()->getAttributeOptionText($attribute, $optionId, $store);
    }

    /**
     * @return \Mycostum\IntegraCommerce\Model\ResourceModel\Eav\Entity\Attribute\Option
     */
    public function getEavAttributeOptionResource()
    {
        /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\Eav\Entity\Attribute\Option $resource */
        $resource = $this->context
            ->objectManager()
            ->create(\Mycostum\IntegraCommerce\Model\ResourceModel\Eav\Entity\Attribute\Option::class);
        
        return $resource;
    }
}
