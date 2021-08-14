<?php

namespace Mycostum\IntegraCommerce\Model;

use Mycostum\IntegraCommerce\Api\Data\OrderInterface;
use Mycostum\IntegraCommerce\Api\OrderRepositoryInterface;
use Mycostum\IntegraCommerce\Model\OrderFactory as OrderFactory;
use Mycostum\IntegraCommerce\Model\ResourceModel\OrderFactory as OrderResourceFactory;

class OrderRepository implements OrderRepositoryInterface
{

    /** @var OrderFactory */
    protected $orderFactory;

    /** @var OrderResourceFactory */
    protected $orderResourceFactory;


    public function __construct(
        OrderFactory $orderFactory,
        OrderResourceFactory $orderResourceFactory
    ) {
        $this->orderFactory         = $orderFactory;
        $this->orderResourceFactory = $orderResourceFactory;
    }


    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(OrderInterface $order)
    {
        $this->getResource()->save($order);
        return $order;
    }


    /**
     * @param int $orderId
     *
     * @return OrderInterface
     */
    public function getByOrderId($orderId)
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->create();
        $this->getResource()->load($order, $orderId, 'order_id');

        return $order;
    }


    /**
     * @return \Mycostum\IntegraCommerce\Model\ResourceModel\Order
     */
    protected function getResource()
    {
        return $this->orderResourceFactory->create();
    }
}
