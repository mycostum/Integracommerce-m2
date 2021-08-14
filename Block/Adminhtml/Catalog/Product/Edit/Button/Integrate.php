<?php

namespace Mycostum\IntegraCommerce\Block\Adminhtml\Catalog\Product\Edit\Button;

use Mycostum\IntegraCommerce\Block\Widget\Button\GenericButton;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Backend\Block\Widget\Context as WidgetContext;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Integrate extends GenericButton implements ButtonProviderInterface
{

    /** @var ActionContext */
    protected $actionContext;

    /**
     * Integrate constructor.
     *
     * @param WidgetContext $widgetContext
     * @param ActionContext $actionContext
     */
    public function __construct(WidgetContext $widgetContext, ActionContext $actionContext)
    {
        parent::__construct($widgetContext);
        $this->actionContext = $actionContext;
    }

    /**
     * Retrieve button-specified settings
     *
     * @return array|bool
     */
    public function getButtonData()
    {
        if (!$this->getProductId()) {
            return false;
        }

        return [
            'label'      => __('Send to IntegraCommerce'),
            'class'      => 'action-secondary',
            'on_click'   => sprintf("location.href = '%s';", $this->getIntegrateUrl($this->getProductId())),
            'sort_order' => 20,
        ];
    }

    /**
     * Get integrate URL
     *
     * @param integer $productId
     *
     * @return string
     */
    public function getIntegrateUrl($productId)
    {
        return $this->getUrl('mycostum_integracommerce/integrate_catalog/product', ['id' => $productId]);
    }

    /**
     * @return integer|null
     */
    protected function getProductId()
    {
        $productId = $this->getRequest()->getParam('id', null);
        return $productId;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    protected function getRequest()
    {
        return $this->actionContext->getRequest();
    }
}
