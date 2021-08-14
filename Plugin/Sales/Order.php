<?php

namespace Mycostum\IntegraCommerce\Plugin\Sales;

class Order
{

    /** @var ExtensionAttribute */
    protected $integracommerceExtensionAttribute;


    /**
     * Order constructor.
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
     * @param \Magento\Sales\Api\Data\OrderInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $result
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterLoad(
        \Magento\Sales\Api\Data\OrderInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $result
    ) {
        return $this->integracommerceExtensionAttribute->get($result);
    }
}
