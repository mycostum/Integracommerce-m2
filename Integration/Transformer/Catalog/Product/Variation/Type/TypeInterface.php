<?php

namespace Mycostum\IntegraCommerce\Integration\Transformer\Catalog\Product\Variation\Type;

use Magento\Catalog\Model\Product;
use IntegraCommerce\Api\EntityInterface\Catalog\Product as ProductEntityInterface;

interface TypeInterface
{
    
    /**
     * @param Product                $product
     * @param ProductEntityInterface $interface
     *
     * @return $this
     */
    public function create(Product $product, ProductEntityInterface $interface);
}
