<?php

namespace Mycostum\IntegraCommerce\Observer\Sales\Order;

use Mycostum\IntegraCommerce\Observer\Sales\AbstractSales;
use Magento\Framework\Event\Observer;

class LogOrderDetailsOnImportException extends AbstractSales
{
    
    /**
     * @param Observer $observer
     *
     * @return void
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (true === $this->context->registryManager()->registry('disable_order_log')) {
            return;
        }
        
        /**
         * @var \Exception $exception
         * @var array      $orderData
         */
        $exception = $observer->getData('exception');
        $orderData = (array) $observer->getData('order_data');
        
        if (!$exception || !$orderData) {
            return;
        }
        
        $orderCode = $this->arrayExtract($orderData, 'code');
        
        $data = [
            'entity_id'       => null,
            'reference'       => (string) $orderCode,
            'entity_type'     => \Mycostum\IntegraCommerce\Model\Entity::TYPE_SALES_ORDER,
            'status'          => \Mycostum\IntegraCommerce\Model\Queue::STATUS_FAIL,
            'process_type'    => \Mycostum\IntegraCommerce\Model\Queue::PROCESS_TYPE_IMPORT,
            'messages'        => $exception->getMessage(),
            'additional_data' => json_encode($orderData),
            'can_process'     => false,
            'store_id'        => (int) $this->getStore()->getId(),
        ];
        
        /** @var \Mycostum\IntegraCommerce\Model\Queue $queue */
        $queue = $this->queueRepository->create($data);
        $this->queueRepository->save($queue);
    }
}
