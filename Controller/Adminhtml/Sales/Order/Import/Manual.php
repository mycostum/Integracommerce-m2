<?php

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Sales\Order\Import;

class Manual extends AbstractImport
{

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $pageResult */
        $pageResult = $this->createPageResult();

        $this->_setActiveMenu('Mycostum_IntegraCommerce::manual_import');

        $pageResult->getConfig()
            ->getTitle()
            ->prepend(__('Manual Import'));

        return $pageResult;
    }
}
