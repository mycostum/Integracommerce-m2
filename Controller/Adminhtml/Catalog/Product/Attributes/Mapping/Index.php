<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Catalog\Product\Attributes\Mapping;

class Index extends AbstractMapping
{
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mycostum_IntegraCommerce::integracommerce_product_attributes_mapping';

    /**
     * Start by creating your controller's logic...
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->createPageResult();
        
        $title = $resultPage->getConfig()->getTitle();
        $title->prepend(__('IntegraCommerce'));
        $title->prepend(__('Product Attributes Mapping'));
        
        return $resultPage;
    }
}
