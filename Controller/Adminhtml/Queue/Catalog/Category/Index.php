<?php

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Queue\Catalog\Category;

use Mycostum\IntegraCommerce\Controller\Adminhtml\AbstractController;

class Index extends AbstractController
{
    
    const ADMIN_RESOURCE = 'Mycostum_IntegraCommerce::integracommerce_queues_catalog_category';

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page = $this->createPageResult();
    
        $page->setActiveMenu('Mycostum_IntegraCommerce::queue_catalog_category');
        
        $title = $page->getConfig()->getTitle();
        $title->prepend(__('IntegraCommerce'));
        $title->prepend(__('Queue'));
        $title->prepend(__('Catalog Category Queue'));
        
        return $page;
    }
}
