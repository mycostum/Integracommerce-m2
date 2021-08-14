<?php

namespace Mycostum\IntegraCommerce\Plugin\Sales;

class OrderRepository
{
    /** @var \Mycostum\IntegraCommerce\Support\Order\ExtensionAttribute */
    protected $integracommerceExtensionAttribute;


    /**
     * OrderRepository constructor.
     *
     * @param \Mycostum\IntegraCommerce\Support\Order\ExtensionAttribute $extensionAttribute
     */
    public function __construct(
        \Mycostum\IntegraCommerce\Support\Order\ExtensionAttribute $extensionAttribute
    ) {
        $this->integracommerceExtensionAttribute = $extensionAttribute;
    }


    /**
     * Set IntegraCommerce data in order extension attributes
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $result
     *
     * @return mixed
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $result
    ) {
        return $this->integracommerceExtensionAttribute->get($result);
    }



    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        $entities
    ) {

        foreach ($entities->getItems() as $entity) {
            $this->afterGet($subject, $entity);
        }

        return $entities;
    }
}
