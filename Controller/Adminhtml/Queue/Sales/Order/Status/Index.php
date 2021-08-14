<?php

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Queue\Sales\Order\Status;

use Mycostum\IntegraCommerce\Controller\Adminhtml\AbstractController;

class Index extends AbstractController
{
    
    const ADMIN_RESOURCE = 'Mycostum_IntegraCommerce::integracommerce_queues_sales_order_status';

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $page */
        $page = $this->createPageResult();
    
        $page->setActiveMenu('Mycostum_IntegraCommerce::queue_sales_order_status');
        
        $page->getConfig()
            ->getTitle()
            ->prepend(__('Sales Order Status'));

        return $page;
    }
}
