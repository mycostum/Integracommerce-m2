<?php

namespace Mycostum\IntegraCommerce\Cron\Queue\Sales;

use Mycostum\IntegraCommerce\Cron\AbstractCron;
use Magento\Cron\Model\Schedule;
use Magento\Store\Api\Data\StoreInterface;

class Order extends AbstractCron
{
    
    /**
     * This method is not mapped (being used) anywhere because it can be harmful to store performance.
     * This is just a method created for tests and used when there's no order in the queue (IntegraCommerce) to be consumed.
     *
     * @param Schedule $schedule
     */
    public function execute(Schedule $schedule)
    {
        $this->processStoreIteration($this, 'executeIntegration', $schedule);
    }
    
    /**
     * @param Schedule       $schedule
     * @param StoreInterface $store
     *
     * @throws \Exception
     */
    public function executeIntegration(Schedule $schedule, StoreInterface $store)
    {
        if (!$this->canRun($schedule, $store->getId())) {
            return;
        }
        
        /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Sales\Order $integrator */
        $integrator = $this->createObject(\Mycostum\IntegraCommerce\Integration\Integrator\Sales\Order::class);
        
        /** @var \Mycostum\IntegraCommerce\Integration\Processor\Sales\Order $orderProcessor */
        $orderProcessor = $this->createObject(\Mycostum\IntegraCommerce\Integration\Processor\Sales\Order::class);
        
        /** @var \Mycostum\IntegraCommerce\Integration\Processor\Sales\Order\Status $orderStatusProcessor */
        $orderStatusProcessor = $this->createObject(\Mycostum\IntegraCommerce\Integration\Processor\Sales\Order\Status::class);
        
        $orders = (array) $integrator->orders();
        
        foreach ($orders as $orderData) {
            try {
                /** @var \Magento\Sales\Api\Data\OrderInterface $order */
                $order = $orderProcessor->createOrder($orderData);
            } catch (\Exception $e) {
                $this->context
                    ->helperContext()
                    ->logger()
                    ->critical($e);
                continue;
            }
            
            if (!$order || !$order->getEntityId()) {
                continue;
            }
            
            $statusType = $this->arrayExtract($orderData, 'status/type');
            $statusCode = $this->arrayExtract($orderData, 'status/code');
            // $statusLabel = $this->arrayExtract($orderData, 'status/label');
    
            $orderStatusProcessor->processOrderStatus($statusCode, $statusType, $order);
            
            $message  = $schedule->getMessages();
            
            if ($order->getData('is_created')) {
                $message .= __(
                    'Order ID %s was successfully created in store %s.',
                    $order->getIncrementId(),
                    $store->getName()
                );
            } elseif ($order->hasDataChanges()) {
                $message .= __(
                    'Order ID %s was updated in store %s.',
                    $order->getIncrementId(),
                    $store->getName()
                );
            }
            
            $schedule->setMessages($message);
        }
    }

    /**
     * @param Schedule $schedule
     * @param int|null $storeId
     *
     * @return bool
     */
    protected function canRun(Schedule $schedule, $storeId = null)
    {
        if (!$this->cronConfig()->salesOrderQueue()->isEnabled($storeId)) {
            $schedule->setMessages(__('Sales Order Queue Cron is Disabled'));
            return false;
        }
        
        return parent::canRun($schedule, $storeId);
    }
}
