<?php

namespace Mycostum\IntegraCommerce\Support\Order;

use Mycostum\IntegraCommerce\Support\Api\ExtensionAttributeInterface;

class ExtensionAttribute implements ExtensionAttributeInterface
{

    /** @var \Magento\Sales\Api\OrderRepositoryInterface  */
    protected $orderRelationRepository;

    /** @var \Mycostum\IntegraCommerce\Api\Data\OrderInterfaceFactory  */
    protected $integracommerceOrderFactory;

    /** @var \Mycostum\IntegraCommerce\Model\Backend\Session\Quote  */
    protected $quoteSession;

    /** @var \Mycostum\IntegraCommerce\Api\OrderRepositoryInterface  */
    protected $integracommerceOrderRepository;

    /** @var \Magento\Sales\Api\Data\OrderExtensionFactory  */
    protected $orderExtensionFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * ExtensionAttribute constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRelationRepository
     * @param \Mycostum\IntegraCommerce\Api\Data\OrderInterfaceFactory $integracommerceOrderFactory
     * @param \Mycostum\IntegraCommerce\Api\OrderRepositoryInterface $integracommerceOrderRepository
     * @param \Mycostum\IntegraCommerce\Model\Backend\Session\Quote $quoteSession
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRelationRepository,
        \Mycostum\IntegraCommerce\Api\Data\OrderInterfaceFactory $integracommerceOrderFactory,
        \Mycostum\IntegraCommerce\Api\OrderRepositoryInterface $integracommerceOrderRepository,
        \Mycostum\IntegraCommerce\Model\Backend\Session\Quote $quoteSession,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Magento\Framework\App\State $appState
    ) {
        $this->orderRelationRepository  = $orderRelationRepository;
        $this->integracommerceOrderFactory       = $integracommerceOrderFactory;
        $this->integracommerceOrderRepository    = $integracommerceOrderRepository;
        $this->quoteSession             = $quoteSession;
        $this->orderExtensionFactory    = $orderExtensionFactory;
        $this->appState                 = $appState;
    }


    /**
     * Get IntegraCommerce order data and set it into order extension attributes
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function get(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Mycostum\IntegraCommerce\Api\OrderRepositoryInterface $relation */
        $relation = $this->integracommerceOrderRepository->getByOrderId($order->getEntityId());

        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes) {
            $order->getExtensionAttributes()->setIntegracommerceInfo($relation);
            return $order;
        }

        $extensionAttributes = $this->orderExtensionFactory->create();
        $extensionAttributes->setIntegracommerceInfo($relation);
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }


    /**
     * Save IntegraCommerce order data custom extension attribute table
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Exception
     */
    public function save(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        if ($order->getPayment()->getMethod() != \Mycostum\IntegraCommerce\Model\Payment\Method\Standard::CODE) {
            return $order;
        }

        try {

            $extensionAttribute = $order->getExtensionAttributes();
            if (!$extensionAttribute) {
                return $order;
            }

            /** @var \Mycostum\IntegraCommerce\Api\Data\OrderInterface $relation */
            $relation = $extensionAttribute->getIntegracommerceInfo();

            if (!$relation) {

                /** @var \Mycostum\IntegraCommerce\Model\Backend\Session\Quote $sessionQuote */
                $sessionQuote = $this->quoteSession->getQuote();
                if (!$sessionQuote || !$sessionQuote->getIntegracommerceCode()) {
                    return $order;
                }

                $relation = $this->integracommerceOrderFactory->create();
                $relation->setOrderId($order->getId())
                    ->setStoreId($order->getStoreId())
                    ->setCode($sessionQuote->getIntegracommerceCode())
                    ->setChannel($sessionQuote->getIntegracommerceChannel())
                    ->setInterest($sessionQuote->getIntegracommerceInterest())
                    ->setDataSource(json_encode($sessionQuote->getIntegracommerceData()));

            }

            $this->integracommerceOrderRepository->save($relation);
            $order->getExtensionAttributes()->setIntegracommerceInfo($relation);

        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            throw new \Exception($e);
        }

        return $order;
    }
}