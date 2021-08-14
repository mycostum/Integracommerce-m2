<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Model\Config\Source\Catalog\Product;

use Mycostum\IntegraCommerce\Model\Config\Source\AbstractSource;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaFactory;

class Attributes extends AbstractSource
{
    
    /** @var ProductAttributeRepositoryInterface */
    protected $productAttributeRepository;

    /** @var $searchCriteriaFactory */
    protected $searchCriteriaFactory;

    /** @var \Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute */
    protected $attributeHelper;

    
    /**
     * Attributes constructor.
     *
     * @param \Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute $attributeHelper
     * @param ProductAttributeRepositoryInterface               $productAttributeRepository
     * @param SearchCriteriaFactory                             $searchCriteriaFactory
     */
    public function __construct(
        \Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute $attributeHelper,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaFactory $searchCriteriaFactory
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaFactory      = $searchCriteriaFactory;
        $this->attributeHelper            = $attributeHelper;
    }


    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $attributes     = [];
        $searchCriteria = $this->searchCriteriaFactory->create();
        $result         = $this->productAttributeRepository->getList($searchCriteria);
        
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($result->getItems() as $attribute) {
            if ($this->attributeHelper->isAttributeCodeInBlacklist($attribute->getAttributeCode())) {
                continue;
            }

            $attributes[$attribute->getId()] = $attribute->getName();
        }
        
        return $attributes;
    }
}
