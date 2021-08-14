<?php

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Sales\Order\Import;

use Mycostum\IntegraCommerce\Controller\Adminhtml\AbstractController;
use Mycostum\IntegraCommerce\Helper\Sales\Order\Created\Message;
use Magento\Backend\App\Action\Context as BackendContext;
use Mycostum\IntegraCommerce\Helper\Context as HelperContext;
use Mycostum\IntegraCommerce\Integration\Integrator\Sales\OrderFactory as OrderIntegratorFactory;
use Mycostum\IntegraCommerce\Integration\Processor\Sales\OrderFactory as OrderProcessorFactory;

abstract class AbstractImport extends AbstractController
{
    
    /** @var OrderIntegratorFactory */
    protected $orderIntegratorFactory;
    
    /** @var OrderProcessorFactory */
    protected $orderProcessorFactory;
    
    /** @var Message */
    protected $message;

    public function __construct(
        BackendContext $context,
        HelperContext $helperContext,
        OrderIntegratorFactory $orderIntegratorFactory,
        OrderProcessorFactory $orderProcessorFactory,
        Message $message
    ) {
        parent::__construct($context, $helperContext);
        
        $this->orderIntegratorFactory = $orderIntegratorFactory;
        $this->orderProcessorFactory  = $orderProcessorFactory;
        $this->message                = $message;
    }

    /**
     * @return \Mycostum\IntegraCommerce\Integration\Integrator\Sales\Order
     */
    protected function getOrderIntegrator()
    {
        return $this->orderIntegratorFactory->create();
    }

    /**
     * @return \Mycostum\IntegraCommerce\Integration\Processor\Sales\Order
     */
    protected function getOrderProcessor()
    {
        return $this->orderProcessorFactory->create();
    }
}
