<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Model\ResourceModel\Catalog\Product\Attributes\Mapping;

use Mycostum\IntegraCommerce\Model\Catalog\Product\Attributes\Mapping;
use Mycostum\IntegraCommerce\Model\ResourceModel\Catalog\Product\Attributes\Mapping as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Mapping::class, ResourceModel::class);
    }
    
    
    /**
     * @param string $code
     *
     * @return \Mycostum\IntegraCommerce\Model\Catalog\Product\Attributes\Mapping
     */
    public function getByIntegraCommerceCode($code)
    {
        /** @var \Mycostum\IntegraCommerce\Model\Catalog\Product\Attributes\Mapping|null $mapping */
        $mapping = $this->getItemByColumnValue('integracommerce_code', $code);
        return $mapping;
    }
    
    
    /**
     * @return $this
     */
    public function setMappedAttributesFilter()
    {
        $this->addFieldToFilter(
            [
                'attribute_id',
                'editable',
            ],
            [
                ['notnull' => true],
                ['eq' => 0]
            ]
        );
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    public function setPendingAttributesFilter()
    {
        $this->addFieldToFilter('attribute_id', ['null' => true])
            ->setEditableFilter()
            ->setEnabledFilter()
            ->setRequiredFilter();
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    public function setEditableFilter()
    {
        $this->addFieldToFilter('editable', 1);
        return $this;
    }
    
    
    /**
     * @return $this
     */
    public function setEnabledFilter()
    {
        $this->addFieldToFilter('enabled', 1);
        return $this;
    }
    
    
    /**
     * @return $this
     */
    public function setRequiredFilter()
    {
        $this->addFieldToFilter('required', 1);
        return $this;
    }
}
