<?php

namespace Mycostum\IntegraCommerce\Model\ResourceModel\Queue\Collection;

use Mycostum\IntegraCommerce\Model\Entity;

class Product extends \Mycostum\IntegraCommerce\Model\ResourceModel\Queue\Collection
{
    
    /**
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->setEntityTypeFilter(Entity::TYPE_CATALOG_PRODUCT);
        
        parent::_beforeLoad();
        return $this;
    }
}
