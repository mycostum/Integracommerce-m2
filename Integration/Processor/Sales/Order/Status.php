<?php

namespace Mycostum\IntegraCommerce\Integration\Processor\Sales\Order;

use Mycostum\IntegraCommerce\Model\Config\Source\Integracommerce\Status\Type as IntegraCommerceStatusType;
use Mycostum\IntegraCommerce\Integration\Processor\AbstractProcessor;
use Mycostum\IntegraCommerce\Integration\Context as IntegrationContext;
use Mycostum\IntegraCommerce\StoreConfig\Context as ConfigContext;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\DB\Transaction as DBTransaction;

class Status extends AbstractProcessor
{

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var ConfigContext */
    protected $configContext;


    public function __construct(
        ConfigContext $configContext,
        IntegrationContext $integrationContext,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($integrationContext);

        $this->orderRepository = $orderRepository;
        $this->configContext   = $configContext;
    }


    /**
     * @param string               $integracommerceStatusCode
     * @param string               $integracommerceStatusType
     * @param Order|OrderInterface $order
     *
     * @return bool|$this
     *
     * @throws \Exception
     */
    public function processOrderStatus($integracommerceStatusCode, $integracommerceStatusType, Order $order)
    {
        if (!$this->validateOrderStatusType($integracommerceStatusType)) {
            return false;
        }

        $state = $this->getStateByIntegracommerceStatusType($integracommerceStatusType);

        if ($order->getState() == $state) {
            return false;
        }

        /**
         * If order is CANCELED in IntegraCommerce.
         */
        if ($state == Order::STATE_CANCELED) {
            try {
                $this->cancelOrder($order);
            } catch (\Exception $e) {
                return false;
            }

            return true;
        }

        /**
         * If order is APPROVED in IntegraCommerce.
         */
        if ($state == Order::STATE_PROCESSING) {
            try {
                $this->invoiceOrder($order);
            } catch (\Exception $e) {
                return false;
            }

            return true;
        }

        $message = __('Change automatically by IntegraCommerce. Status %1, Type %2.', $integracommerceStatusCode, $integracommerceStatusType);

        $order->setState($state)
            ->setData('is_updated', true)
            ->addStatusHistoryComment($message, true);

        try {
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


    /**
     * @param string $integracommerceStatusType
     *
     * @return string
     */
    public function getStateByIntegracommerceStatusType($integracommerceStatusType)
    {
        switch ($integracommerceStatusType) {
            case IntegraCommerceStatusType::TYPE_APPROVED:
                return Order::STATE_PROCESSING;
            case IntegraCommerceStatusType::TYPE_CANCELED:
                return Order::STATE_CANCELED;
            case IntegraCommerceStatusType::TYPE_DELIVERED:
            case IntegraCommerceStatusType::TYPE_SHIPPED:
                return Order::STATE_COMPLETE;
            case IntegraCommerceStatusType::TYPE_NEW:
            default:
                return Order::STATE_NEW;
        }
    }


    /**
     * @param string $integracommerceStatusType
     *
     * @return bool
     */
    public function validateOrderStatusType($integracommerceStatusType)
    {
        /** @var IntegraCommerceStatusType $source */
        $source = $this->helperContext()->objectManager()->create(IntegraCommerceStatusType::class);
        $allowedTypes = $source->toArray();

        return isset($allowedTypes[$integracommerceStatusType]);
    }


    /**
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    protected function cancelOrder(Order $order)
    {
        if (!$order->canCancel()) {
            $order->addStatusHistoryComment(__('Order is canceled in IntegraCommerce but could not be canceled in Magento.'));
            $this->orderRepository->save($order);

            return false;
        }

        $order->addStatusHistoryComment(__('Order canceled automatically by IntegraCommerce.'));
        $order->cancel();

        $this->orderRepository->save($order);

        return true;
    }


    /**
     * @param Order $order
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    protected function invoiceOrder(Order $order)
    {
        if (!$order->canInvoice()) {
            $comment = __('This order is APPROVED in IntegraCommerce but cannot be invoiced in Magento.');
            $order->addStatusHistoryComment($comment, true);
            $this->orderRepository->save($order);

            return false;
        }

        /** @var Order\Invoice $invoice */
        $invoice = $order->prepareInvoice();
        $invoice->register();

        $comment = __('Invoiced automatically via IntegraCommerce.');
        $invoice->addComment($comment);

        /** @var string $approvedOrdersStatus */
        $approvedOrdersStatus = $this->configContext->salesOrderStatus()->getApprovedOrdersStatus();
        
        $order->setIsInProcess(true);
        $order->setStatus($approvedOrdersStatus);
        $order->addStatusHistoryComment($comment, true);

        $this->getTransaction()
            ->addObject($order)
            ->addObject($invoice)
            ->save();

        return true;
    }


    /**
     * @return DBTransaction
     */
    protected function getTransaction()
    {
        /** @var DBTransaction $transaction */
        $transaction = $this->helperContext()->objectManager()->create(DBTransaction::class);
        return $transaction;
    }
}
