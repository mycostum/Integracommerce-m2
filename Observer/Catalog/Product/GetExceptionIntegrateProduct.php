<?php

namespace Mycostum\IntegraCommerce\Observer\Catalog\Product;

use Mycostum\IntegraCommerce\Observer\Catalog\AbstractCatalog;
use Magento\Framework\Event\Observer;

class GetExceptionIntegrateProduct extends AbstractCatalog
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * GetExceptionIntegrateProduct constructor.
     * @param \Mycostum\IntegraCommerce\Helper\Context $context
     * @param \Mycostum\IntegraCommerce\Model\StoreIteratorInterface $storeIterator
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Mycostum\IntegraCommerce\Integration\Integrator\Sales\Order $orderIntegrator
     * @param \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product $productIntegrator
     * @param \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product\Attribute $productAttributeIntegrator
     * @param \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Category $categoryIntegrator
     * @param \Mycostum\IntegraCommerce\Api\QueueRepositoryInterface $queueRepository
     * @param \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\ProductValidation $productValidation
     * @param \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\CategoryValidation $categoryValidation
     * @param \Mycostum\IntegraCommerce\Model\ResourceModel\QueueFactory $queueResourceFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $typeConfigurableFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Mycostum\IntegraCommerce\Helper\Context $context,
        \Mycostum\IntegraCommerce\Model\StoreIteratorInterface $storeIterator,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Mycostum\IntegraCommerce\Integration\Integrator\Sales\Order $orderIntegrator,
        \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product $productIntegrator,
        \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product\Attribute $productAttributeIntegrator,
        \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Category $categoryIntegrator,
        \Mycostum\IntegraCommerce\Api\QueueRepositoryInterface $queueRepository,
        \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\ProductValidation $productValidation,
        \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\CategoryValidation $categoryValidation,
        \Mycostum\IntegraCommerce\Model\ResourceModel\QueueFactory $queueResourceFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
        parent::__construct(
            $context,
            $storeIterator,
            $orderRepository,
            $productRepository,
            $orderIntegrator,
            $productIntegrator,
            $productAttributeIntegrator,
            $categoryIntegrator,
            $queueRepository,
            $productValidation,
            $categoryValidation,
            $queueResourceFactory,
            $typeConfigurableFactory,
            $timezone
        );
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->canRun()) {
            return;
        }

        if ($this->context->appState()->getAreaCode() != \Magento\Framework\App\Area::AREA_ADMINHTML) {
            return;
        }

        if ($observer->getMethod() != 'prepareIntegrationProduct') {
            return;
        }

        $this->messageManager->addError(
            __('The product cannot be integrated: %1', $observer->getException()->getMessage())
        );
    }
}