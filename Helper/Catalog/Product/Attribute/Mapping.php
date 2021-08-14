<?php

namespace Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute;

use Mycostum\IntegraCommerce\Helper\Context;
use Mycostum\IntegraCommerce\Model\ResourceModel\Catalog\Product\Attributes\Mapping\Collection as AttributesMappingCollection;
use Magento\Framework\Registry;

class Mapping
{
    
    /** @var Context */
    protected $context;
    
    /** @var Registry */
    protected $registry;
    
    /** @var array */
    protected $mappedAttributes = [];

    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->context  = $context;
        $this->registry = $registry;
    }

    /**
     * @param string $integracommerceCode
     *
     * @return mixed|null
     */
    public function getMappedAttribute($integracommerceCode)
    {
        $this->initMappedAttributes();
        
        if (isset($this->mappedAttributes[$integracommerceCode])) {
            return $this->mappedAttributes[$integracommerceCode];
        }
        
        return null;
    }

    /**
     * @return array
     */
    public function getMappedAttributes()
    {
        $this->initMappedAttributes();
        
        return (array) $this->mappedAttributes;
    }

    /**
     * @return $this
     */
    protected function initMappedAttributes()
    {
        if (empty($this->mappedAttributes)) {
            $this->mappedAttributes = [];
            
            /** @var \Mycostum\IntegraCommerce\Model\Catalog\Product\Attributes\Mapping $mappedAttribute */
            foreach ($this->getMappedAttributesCollection() as $mappedAttribute) {
                $this->mappedAttributes[$mappedAttribute->getIntegracommerceCode()] = $mappedAttribute;
            }
        }
        
        return $this;
    }

    /**
     * @return bool
     */
    public function hasPendingAttributesForMapping()
    {
        return (bool) ($this->getPendingAttributesCollection()->getSize() > 0);
    }

    /**
     * @return AttributesMappingCollection
     */
    public function getPendingAttributesCollection()
    {
        $key = 'notification_pending_attributes_collection';
        
        if (!$this->registry->registry($key)) {
            /** @var AttributesMappingCollection $collection */
            $collection = $this->getAttributesMappingCollection()
                ->setPendingAttributesFilter();
            
            $this->registry->register($key, $collection, true);
        }
        
        return $this->registry->registry($key);
    }

    /**
     * @return AttributesMappingCollection
     */
    public function getMappedAttributesCollection()
    {
        $collection = $this->getAttributesMappingCollection()
            ->setMappedAttributesFilter();
        
        return $collection;
    }

    /**
     * @return AttributesMappingCollection
     */
    public function getAttributesMappingCollection()
    {
        /** @var AttributesMappingCollection $collection */
        $collection = $this->context
            ->objectManager()
            ->create(AttributesMappingCollection::class);
        
        return $collection;
    }
}
