<?php

namespace Mycostum\IntegraCommerce\Integration\Processor;

use Mycostum\IntegraCommerce\Functions;
use Mycostum\IntegraCommerce\Integration\Context as IntegrationContext;

abstract class AbstractProcessor
{

    use Functions;


    /** @var IntegrationContext */
    private $integrationContext;


    /**
     * AbstractProcessor constructor.
     *
     * @param IntegrationContext $integrationContext
     */
    public function __construct(IntegrationContext $integrationContext)
    {
        $this->integrationContext = $integrationContext;
    }


    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function logger()
    {
        return $this->helperContext()->logger();
    }


    /**
     * @return \Magento\Framework\Event\ManagerInterface
     */
    protected function eventManager()
    {
        return $this->helperContext()->eventManager();
    }


    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    protected function storeManager()
    {
        return $this->helperContext()->storeManager();
    }


    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    protected function objectManager()
    {
        return $this->helperContext()->objectManager();
    }


    /**
     * @return \Mycostum\IntegraCommerce\Helper\Context
     */
    protected function helperContext()
    {
        return $this->integrationContext()->helperContext();
    }


    /**
     * @return IntegrationContext
     */
    protected function integrationContext()
    {
        return $this->integrationContext;
    }
}
