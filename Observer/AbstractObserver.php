<?php

namespace Mycostum\IntegraCommerce\Observer;

use Mycostum\IntegraCommerce\Functions;
use Mycostum\IntegraCommerce\Helper\Context;

abstract class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{
    
    use Functions;
    
    
    /** @var Context */
    protected $context;
    
    /** @var \Mycostum\IntegraCommerce\Model\StoreIteratorInterface */
    protected $storeIterator;
    
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $orderRepository;
    
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $productRepository;
    
    /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Sales\Order */
    protected $orderIntegrator;
    
    /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product */
    protected $productIntegrator;
    
    /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product\Attribute */
    protected $productAttributeIntegrator;
    
    /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Category */
    protected $categoryIntegrator;
    
    /** @var \Mycostum\IntegraCommerce\Api\QueueRepositoryInterface */
    protected $queueRepository;
    
    /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\ProductValidation */
    protected $productValidation;
    
    /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\CategoryValidation */
    protected $categoryValidation;
    
    /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\QueueFactory */
    protected $queueResourceFactory;
    
    /** @var \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory */
    protected $typeConfigurableFactory;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $timezone;
    
    
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
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->context                    = $context;
        $this->storeIterator              = $storeIterator;
        $this->orderRepository            = $orderRepository;
        $this->productRepository          = $productRepository;
        $this->orderIntegrator            = $orderIntegrator;
        $this->productIntegrator          = $productIntegrator;
        $this->productAttributeIntegrator = $productAttributeIntegrator;
        $this->categoryIntegrator         = $categoryIntegrator;
        $this->queueRepository            = $queueRepository;
        $this->productValidation          = $productValidation;
        $this->categoryValidation         = $categoryValidation;
        $this->queueResourceFactory       = $queueResourceFactory;
        $this->typeConfigurableFactory    = $typeConfigurableFactory;
        $this->timezone                   = $timezone;
    }
    
    
    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    protected function canRun($storeId = null)
    {
        if (!$this->context->configContext()->general()->isModuleEnabled($storeId)) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param null|int $storeId
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStore($storeId = null)
    {
        return $this->context->storeManager()->getStore($storeId);
    }
}
