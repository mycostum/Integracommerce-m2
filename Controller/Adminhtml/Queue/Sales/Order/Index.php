<?php

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Queue\Sales\Order;

use Mycostum\IntegraCommerce\Controller\Adminhtml\AbstractController;

class Index extends AbstractController
{
    
    const ADMIN_RESOURCE = 'Mycostum_IntegraCommerce::integracommerce_queues_sales_order';
    
    
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page = $this->createPageResult();
    
        $page->setActiveMenu('Mycostum_IntegraCommerce::queue_sales_order');
        
        $page->getConfig()
            ->getTitle()
            ->append(__('Sales Order'));

        return $page;
    }
}
