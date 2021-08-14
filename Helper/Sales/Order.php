<?php

namespace Mycostum\IntegraCommerce\Helper\Sales;

use Mycostum\IntegraCommerce\Helper\AbstractHelper;
use Magento\Sales\Model\Order as SalesOrder;

class Order extends AbstractHelper
{

    /**
     * @param string $integracommerceCode
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderId($integracommerceCode)
    {
        /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\Order $orderResource */
        $orderResource = $this->objectManager()->create(\Mycostum\IntegraCommerce\Model\ResourceModel\Order::class);
        $orderId       = $orderResource->getEntityIdByIntegracommerceCode($integracommerceCode);

        return $orderId;
    }

    /**
     * @param string $code
     *
     * @return int
     */
    public function getNewOrderIncrementId($code)
    {
        $useDefaultIncrementId = $this->context->configContext()->salesOrderImport()->useDefaultIncrementId();

        if (!$useDefaultIncrementId) {
            return $code;
        }

        return null;
    }

    /**
     * @param int $orderId (entity_id)
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderIncrementId($orderId)
    {
        /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\Order $orderResource */
        $orderResource = $this->objectManager()->create(\Mycostum\IntegraCommerce\Model\ResourceModel\Order::class);
        $integracommerceCode    = $orderResource->getIntegracommerceCodeByOrderId($orderId);

        return $integracommerceCode;
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPendingOrdersFromIntegraCommerce()
    {
        /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\Order $orderResource */
        $orderResource = $this->objectManager()->create(\Mycostum\IntegraCommerce\Model\ResourceModel\Order::class);
        
        $deniedStates = [
            SalesOrder::STATE_CANCELED,
            SalesOrder::STATE_CLOSED,
            SalesOrder::STATE_COMPLETE,
        ];

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->objectManager()->create(\Magento\Sales\Model\ResourceModel\Order\Collection::class);
        $collection->join(['bso' => $orderResource->getMainTable()], 'bso.order_id = main_table.entity_id');
        $collection->addFieldToFilter('state', ['nin' => $deniedStates]);

        return $collection;
    }
}
