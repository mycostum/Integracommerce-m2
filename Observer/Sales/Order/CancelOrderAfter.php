<?php

namespace Mycostum\IntegraCommerce\Observer\Sales\Order;

use Mycostum\IntegraCommerce\Cron\Queue\Sales\Order\Queue;
use Mycostum\IntegraCommerce\Observer\Sales\AbstractSales;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

class CancelOrderAfter extends AbstractSales
{

    /**
     * @param Observer $observer
     *
     * @throws NoSuchEntityException
     * @return void
     *
     */
    public function execute(Observer $observer)
    {
        if ($this->context->registryManager()->registry(Queue::QUEUE_PROCESS)) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $observer->getData('order');

        if (!$order || !$order->getEntityId()) {
            return;
        }

        $store = $this->getStore($order->getStoreId());

        $this->storeIterator->call($this->orderIntegrator, 'cancel', [$order->getEntityId()], $store);
    }
}
