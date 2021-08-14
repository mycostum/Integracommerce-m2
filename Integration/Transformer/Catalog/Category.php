<?php

namespace Mycostum\IntegraCommerce\Integration\Transformer\Catalog;

use Mycostum\IntegraCommerce\Integration\Transformer\AbstractTransformer;
use Magento\Catalog\Model\Category as CatalogCategory;

class Category extends AbstractTransformer
{
    
    /**
     * @param CatalogCategory $category
     *
     * @return \IntegraCommerce\Api\EntityInterface\Catalog\Category
     */
    public function convert(CatalogCategory $category)
    {
        /** @var \Mycostum\IntegraCommerce\Helper\Catalog\Category $helper */
        $helper = $this->context->objectManager()->create(\Mycostum\IntegraCommerce\Helper\Catalog\Category::class);
        
        /** @var \IntegraCommerce\Api\EntityInterface\Catalog\Category $interface */
        $interface = $this->context->api()->category()->entityInterface();
        $interface->setCode($category->getId())
            ->setName($helper->extractProductCategoryPathString($category));
        
        return $interface;
    }
}
