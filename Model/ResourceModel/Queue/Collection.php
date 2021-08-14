<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Model\ResourceModel\Queue;

use Mycostum\IntegraCommerce\Model\Queue;
use Mycostum\IntegraCommerce\Model\ResourceModel\Queue as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    
    /** @var string */
    const FIELD_ENTITY_TYPE = 'entity_type';
    
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Queue::class, ResourceModel::class);
    }
    
    
    /**
     * @param string $type
     *
     * @return $this
     */
    public function setEntityTypeFilter($type)
    {
        $this->addFieldToFilter(self::FIELD_ENTITY_TYPE, $type);
        return $this;
    }
}
