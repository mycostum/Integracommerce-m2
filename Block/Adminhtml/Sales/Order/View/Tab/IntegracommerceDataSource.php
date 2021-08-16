<?php

namespace Mycostum\IntegraCommerce\Block\Adminhtml\Sales\Order\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;

class IntegracommerceDataSource extends AbstractOrder implements TabInterface
{
    
    /** @var string */
    protected $_template = 'order/view/tab/integracommerce_data_source.phtml';


    /**
     * @param bool $pretty
     * @return bool|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEncodedJsonDataSource($pretty = false)
    {
        if (!$this->getOrder()) {
            return false;
        }
        
        if (!$this->getOrder()->getExtensionAttributes()) {
            return false;
        }
        
        /** @var \Mycostum\IntegraCommerce\Api\Data\OrderInterface $info */
        $info = $this->getOrder()->getExtensionAttributes()->getIntegracommerceInfo();
        
        $decoded = json_decode($info->getDataSource());

        if ($pretty) {
            $decoded  = json_encode($decoded, JSON_PRETTY_PRINT);
        }

        return $decoded;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('IntegraCommerce Data Source');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        if (!$this->getEncodedJsonDataSource()) {
            return false;
        }

        return true;
    }
    
    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
