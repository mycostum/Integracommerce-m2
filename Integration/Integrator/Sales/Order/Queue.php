<?php

namespace Mycostum\IntegraCommerce\Integration\Integrator\Sales\Order;

use Mycostum\IntegraCommerce\Integration\Integrator\Sales\AbstractSales;
use \Magento\Sales\Api\Data\OrderInterface;

class Queue extends AbstractSales
{
    
    /**
     * @return array|bool
     */
    public function nextOrder()
    {
        /** @var \IntegraCommerce\Api\EntityInterface\Sales\Order\Queue $interface */
        $interface = $this->api()->queue()->entityInterface();
        $result    = $interface->orders();
        
        if ($result->exception() || $result->invalid()) {
            return false;
        }
        
        /** @var \IntegraCommerce\Api\Handler\Response\HandlerDefault $result */
        $data = $result->toArray();
        
        if (empty($data)) {
            return false;
        }
        
        return (array) $data;
    }
    
    
    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function deleteByOrder(OrderInterface $order)
    {
        if (!$integracommerceOrderCode = $order->getExtensionAttributes()->getIntegracommerceInfo()->getCode()) {
            return false;
        }
        
        return $this->delete($integracommerceOrderCode);
    }
    
    
    /**
     * @param string $orderCode
     *
     * @return bool
     */
    public function delete($orderCode)
    {
        /** @var \IntegraCommerce\Api\Handler\Response\HandlerInterface $isDeleted */
        $isDeleted = $this->api()->queue()->delete($orderCode);
        return (bool) $isDeleted->success();
    }
}
