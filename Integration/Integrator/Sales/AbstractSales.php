<?php

namespace Mycostum\IntegraCommerce\Integration\Integrator\Sales;

use Mycostum\IntegraCommerce\Integration\Integrator\AbstractIntegrator;

abstract class AbstractSales extends AbstractIntegrator
{
    
    /**
     * @param string $integracommerceCode
     *
     * @return int
     */
    protected function getOrderId($integracommerceCode)
    {
        try {
            /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\Order $resource */
            $resource = $this->context->objectManager()->get(\Mycostum\IntegraCommerce\Model\ResourceModel\Order::class);
            $orderId  = $resource->getEntityIdByIntegracommerceCode($integracommerceCode);
        } catch (\Exception $e) {
            $this->context->helperContext()->logger()->critical($e);
        }
        
        return $orderId;
    }
    
    
    /**
     * @param int $orderId
     *
     * @return string
     */
    protected function getOrderIncrementId($orderId)
    {
        try {
            /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\Order $resource */
            $resource   = $this->context->objectManager()->get(\Mycostum\IntegraCommerce\Model\ResourceModel\Order::class);
            $integracommerceCode = $resource->getIntegracommerceCodeByOrderId((int) $orderId);
        } catch (\Exception $e) {
            $this->context->helperContext()->logger()->critical($e);
        }
        
        return trim($integracommerceCode);
    }
}
