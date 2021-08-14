<?php

namespace Mycostum\IntegraCommerce\Model\Sales;

use Mycostum\IntegraCommerce\Api\OrderManagementInterface;
use Mycostum\IntegraCommerce\Api\OrderRepositoryInterface as OrderRelationRepositoryInterface;
use Mycostum\IntegraCommerce\Helper\Context;
use Magento\Framework\Webapi\Exception;
use Magento\Sales\Api\OrderRepositoryInterface;

class Order implements OrderManagementInterface
{
    /**
     * @var Context
     */
    protected $helperContext;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderRelationRepositoryInterface
     */
    protected $orderRelationRepository;

    public function __construct(
        Context $helperContext,
        OrderRepositoryInterface $orderRepository,
        OrderRelationRepositoryInterface $orderRelationRepository
    )
    {
        $this->helperContext = $helperContext;
        $this->orderRepository = $orderRepository;
        $this->orderRelationRepository = $orderRelationRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function invoice($entityId, $invoiceKey)
    {
        $order = $this->orderRepository->get($entityId);

        /** @var \Mycostum\IntegraCommerce\Model\Order $info */
        $info = $order->getExtensionAttributes()->getIntegracommerceInfo();

        if (!$info) {
            throw new Exception(__('This order doesnt have a IntegraCommerce block info.'));
        }

        if ($info->getInvoiceKey()) {
            throw new Exception(__('This order already have a invoice key.'));
        }

        if (!$info->validateInvoiceKey($invoiceKey)) {
            throw new Exception(__('Invalid invoice key (NF-e Key).'));
        }

        $info->setInvoiceKey($invoiceKey);

        $this->orderRelationRepository->save($info);

        return true;
    }
}
