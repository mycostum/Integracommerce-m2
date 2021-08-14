<?php

namespace Mycostum\IntegraCommerce\Helper\Sales\Order\Created;

use Magento\Sales\Api\Data\OrderInterface;

class Message
{
    
    /**
     * @param OrderInterface $order
     * @param string         $integracommerceCode
     *
     * @return \Magento\Framework\Phrase
     */
    public function getOrderCreationMessage(OrderInterface $order, $integracommerceCode)
    {
        if (true === $order->getData('is_created')) {
            return __(
                'The order code %1 was successfully created. Order ID %2.',
                $integracommerceCode,
                $order->getIncrementId()
            );
        }
        
        if (true === $order->getData('is_updated')) {
            return __(
                'The order code %1 already exists and had its status updated. Order ID %2.',
                $integracommerceCode,
                $order->getIncrementId()
            );
        }
        
        return __(
            'The order code %1 already exists and did not need to be updated. Order ID %2.',
            $integracommerceCode,
            $order->getIncrementId()
        );
    }

    /**
     * @param string $integracommerceCode
     *
     * @return \Magento\Framework\Phrase
     */
    public function getNonExistentOrderMessage($integracommerceCode)
    {
        return __('The order reference "%1" does not exist in Integracommerce.', $integracommerceCode);
    }
    
    /**
     * @param string $integracommerceCode
     *
     * @return \Magento\Framework\Phrase
     */
    public function getNotCreatedOrderMessage($integracommerceCode)
    {
        return __('The order reference "%1" could not be created. See the logs for more details.', $integracommerceCode);
    }
}
