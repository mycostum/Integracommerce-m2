<?php

namespace Mycostum\IntegraCommerce\Ui\Component\DataProvider\SearchResult\Queue\Catalog;

use Mycostum\IntegraCommerce\Ui\Component\DataProvider\SearchResult\Queue;

class Category extends Queue
{
    
    /** @var string */
    protected $entityType = 'catalog_category';
    
    
    /**
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->join(
            [
                'e' => $this->getTable('catalog_category_entity')
            ],
            'e.entity_id = queue.entity_id',
            []
        );
        
        parent::_beforeLoad();
        return $this;
    }
}
