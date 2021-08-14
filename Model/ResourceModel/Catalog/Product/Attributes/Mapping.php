<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Model\ResourceModel\Catalog\Product\Attributes;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Mapping extends AbstractDb
{
    /**
     * Initialize database relation.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mycostum_integracommerce_product_attributes_mapping', 'id');
    }
}
