<?php

namespace Mycostum\IntegraCommerce\Integration\Support\Sales\Order;

use Mycostum\IntegraCommerce\Functions;
use Mycostum\IntegraCommerce\Integration\Context as IntegrationContext;
use Mycostum\IntegraCommerce\Model\Backend\Session\Quote as AdminSessionQuote;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order;
use Magento\Store\Api\Data\StoreInterface;
use Mycostum\IntegraCommerce\Model\Sales\AdminOrder\Create as AdminOrderCreate;

class Create
{

    use Functions, \Mycostum\IntegraCommerce\Traits\Customer;
    
    /** @var string */
    const ADDRESS_TYPE_BILLING  = 'billing';
    
    /** @var string */
    const ADDRESS_TYPE_SHIPPING = 'shipping';
    
    /** @var string */
    const CARRIER_PREFIX = 'mycostum_integracommerce_';
    

    /** @var StoreInterface */
    private $store;

    /** @var array */
    private $data = [];

    /** @var AdminOrderCreate */
    protected $creator;

    /** @var IntegrationContext */
    protected $context;

    /** @var \Magento\Quote\Api\CartRepositoryInterface */
    protected $quoteRepository;

    /** @var \Magento\Framework\App\State */
    protected $state;

    /**
     * Create constructor.
     * @param IntegrationContext $context
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param null $store
     */
    public function __construct(
        IntegrationContext $context,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\App\State $state,
        $store = null)
    {
        $this->context = $context;
        $this->quoteRepository = $quoteRepository;

        $data = [
            'session' => [
                'store_id' => $this->getStore($store)->getId(),
            ],
            'order'   => [
                'currency' => $this->getStore($store)->getCurrentCurrencyCode(),
            ],
        ];
        $this->state = $state;

        $this->merge($data);
    }


    /**
     * @param DataObject $order
     *
     * @return $this
     */
    public function setOrderInfo(DataObject $order)
    {
        $data = [
            'order' => [
                'increment_id'      => $order->getData('increment_id'),
                'send_confirmation' => $order->getData('send_confirmation')
            ],
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param null|string $comment
     *
     * @return $this
     */
    public function setComment($comment = null)
    {
        $data = [
            'order' => [
                'comment' => [
                    'customer_note'        => $comment,
                    'customer_note_notify' => false
                ]
            ],
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param array $data
     *
     * @return $this
     */
    public function addProduct(array $data)
    {
        $productId = (int)  $this->arrayExtract($data, 'product_id');
        $qty       = (float) $this->arrayExtract($data, 'qty');

        if (!$productId) {
            return $this;
        }

        $data = [
            'products' => [
                [
                    'product' => $data,
                    'config'  => [
                        'qty' => $qty ? $qty : 1,
                    ],
                ]
            ]
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param string $method
     *
     * @return $this
     */
    public function setPaymentMethod($method = 'checkmo')
    {
        $data = [
            'payment' => [
                'method' => $method,
            ]
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param float $discount
     *
     * @return $this
     */
    public function setDiscountAmount($discount)
    {
        $data = [
            'order' => [
                'discount_amount' => (float) $discount,
            ]
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param float $discount
     *
     * @return $this
     */
    public function setInterestAmount($discount)
    {
        $data = [
            'order' => [
                'interest' => (float) $discount,
            ]
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param $data
     *
     * @return $this
     */
    public function setIntegracommerceData($data)
    {
        $data = [
            'order' => [
                'integracommerce_code'       => $this->arrayExtract($data, 'code'),
                'integracommerce_channel'    => $this->arrayExtract($data, 'channel'),
                'integracommerce_data'       => $data,
                'integracommerce_interest'   => $this->arrayExtract($data, 'interest'),
            ]
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param null  $title
     * @param null  $carrier
     * @param float $cost
     *
     * @return $this
     */
    public function setShippingMethod($title = null, $carrier = null, $cost = 0.0000)
    {
        if (!$title) {
            $title = 'Standard';
        }

        /** @var string $methodCode */
        $methodCode = $this->normalizeString($title);

        $data = [
            'order' => [
                'shipping_method'        => $carrier . '_' . $methodCode,
                'shipping_method_code'   => $methodCode,
                'shipping_title'         => $title,
                'shipping_carrier'       => $carrier,
                'shipping_cost'          => (float) $cost,
            ]
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @param CustomerInterface $customer
     *
     * @return $this
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $data = [
            'order' => [
                'account' => [
                    'group_id'      => $customer->getGroupId(),
                    'email'         => $customer->getEmail(),
                ]
            ],
            'session' => [
                'customer_id' => $customer->getId()
            ]
        ];

        if ($customer->getTaxvat()) {
            $data['order']['account']['taxvat'] = $customer->getTaxvat();
        }

        $this->merge($data);

        return $this;
    }


    /**
     * @param string           $type
     * @param AddressInterface $address
     *
     * @return $this
     */
    public function addOrderAddress(AddressInterface $address, $type)
    {
        $data = [
            'order' => [
                "{$type}_address" => [
                    'customer_address_id' => $address->getId(),
                    'prefix'              => $address->getPrefix(),
                    'firstname'           => $address->getFirstname(),
                    'middlename'          => $address->getMiddlename(),
                    'lastname'            => $address->getLastname(),
                    'suffix'              => $address->getSuffix(),
                    'company'             => $address->getCompany(),
                    'street'              => $address->getStreet(),
                    'city'                => $address->getCity(),
                    'country_id'          => $address->getCountryId(),
                    'region'              => $address->getRegion()->getRegion(),
                    'region_id'           => $address->getRegionId(),
                    'postcode'            => $address->getPostcode(),
                    'telephone'           => $this->formatPhone($address->getTelephone()),
                    'fax'                 => $this->formatPhone($address->getFax()),
                ]
            ]
        ];

        $this->merge($data);

        return $this;
    }


    /**
     * @return $this
     */
    public function reset()
    {
        $this->creator = null;
        $this->data    = [];
        $this->store   = null;

        return $this;
    }


    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->getOrderCreator()->getQuote();
    }


    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }
    
    
    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function resetQuote()
    {
        $this->getQuote()->setTotalsCollectedFlag(false);
        return $this;
    }


    /**
     * @return AdminSessionQuote
     */
    protected function getSession()
    {
        /** @var AdminSessionQuote $session */
        $session = $this->context->objectManager()->get(AdminSessionQuote::class);
        return $session;
    }


    /**
     * Retrieve order create model
     *
     * @return \Mycostum\IntegraCommerce\Model\Sales\AdminOrder\Create
     */
    protected function getOrderCreator()
    {
        if (!$this->creator) {
            $this->creator = $this->context
                ->objectManager()
                ->create(\Mycostum\IntegraCommerce\Model\Sales\AdminOrder\Create::class);
        }

        return $this->creator;
    }


    /**
     * Initialize order creation session data
     *
     * @param array $data
     *
     * @return $this
     */
    protected function initSession($data)
    {
        /* Get/identify customer */
        if (!empty($data['customer_id'])) {
            $this->getSession()->setCustomerId((int) $this->arrayExtract($data, 'customer_id'));
        }

        /* Get/identify store */
        if (!empty($data['store_id'])) {
            $this->getSession()->setStoreId((int) $this->arrayExtract($data, 'store_id'));
        }

        return $this;
    }


    /**
     * @return Order|null
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create()
    {
        $registry = $this->context->helperContext()->registryManager();

        $orderData = $this->data;
        $order     = null;

        if (!empty($orderData)) {

            $this->getSession()->clear();
            $this->initSession($this->arrayExtract($orderData, 'session'));

            try {

                $this->processQuote($orderData);
                $paymentData = $this->arrayExtract($orderData, 'payment');

                if (!empty($paymentData)) {
                    $this->getOrderCreator()
                        ->setPaymentData($paymentData);

                    $this->getQuote()
                        ->getPayment()
                        ->addData($paymentData);
                }

                $this->context->eventManager()->dispatch('mycostum_integracommerce_order_import_before', [
                    'order'      => $order,
                    'order_data' => $orderData,
                    'creator'    => $this,
                ]);

                /** @var Order $order */
                $order = $this->getOrderCreator()
                    ->importPostData($this->arrayExtract($orderData, 'order'))
                    ->createOrder();

                $eventName = 'mycostum_integracommerce_order_import_success';
            } catch (\Exception $e) {
                /** remove quote */
                $this->quoteRepository->delete($this->getQuote());
//                $this->getSession()->clear();
                /** end */

                $this->context->helperContext()->logger()->critical($e);
                $eventName = 'mycostum_integracommerce_order_import_fail';
            }

            $this->context->eventManager()->dispatch($eventName, [
                'order'      => $order,
                'order_data' => $orderData,
            ]);

            $this->getSession()->clear();
            $registry->unregister('rule_data');
        }

        return $order;
    }


    /**
     * @param array $data
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processQuote($data = [])
    {
        $this->initArea();
        
        $orderData = (array) $this->arrayExtract($data, 'order', []);

        /** @var \Mycostum\IntegraCommerce\Model\Sales\AdminOrder\Create $orderCreator */
        $orderCreator = $this->getOrderCreator();

        /* Saving order data */
        if (!empty($orderData)) {
            $orderCreator->importPostData($orderData);
        }

        if ($incrementId = $this->arrayExtract($orderData, 'increment_id')) {
            $this->getQuote()->setReservedOrderId($incrementId);
        }

        /* Just like adding products from Magento admin grid */
        $products = (array) $this->arrayExtract($data, 'products', []);

        $inventoryErrors = [];

        /** @var array $product */
        foreach ($products as $item) {
            try {
                $orderCreator->addProductByData($item);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $inventoryErrors[] = $e->getMessage();
            }
        }

        if ($inventoryErrors) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(implode('.\n', $inventoryErrors))
            );
        }

        $this->registerIntegracommerceData($orderData);
        $this->registerDiscount($orderData);
        $this->registerInterest($orderData);

        $shippingMethod       = (string) $this->arrayExtract($data, 'order/shipping_method');
        $shippingMethodCode   = (string) $this->arrayExtract($data, 'order/shipping_method_code');
        $shippingCarrier      = (string) $this->arrayExtract($data, 'order/shipping_carrier');
        $shippingTitle        = (string) $this->arrayExtract($data, 'order/shipping_title');
        $shippingAmount       = (float)  $this->arrayExtract($data, 'order/shipping_cost');

        $this->getQuote()
            ->setFixedShippingAmount($shippingAmount)
            ->setFixedShippingMethod($shippingMethod)
            ->setFixedShippingMethodCode($shippingMethodCode)
            ->setFixedShippingCarrier($shippingCarrier)
            ->setFixedShippingTitle($shippingTitle);

        /* Add payment data */
        $payment = $this->arrayExtract($data, 'payment', []);
        if (!empty($payment)) {
            $orderCreator->getQuote()
                ->getPayment()
                ->addData($payment);
        }

        /* Collect shipping rates */
        $this->resetQuote();
        $this->getShippingAddress()->unsetData('cached_items_all');
        $orderCreator->collectShippingRates();

        $orderCreator->initRuleData()
            ->saveQuote();

        return $this;
    }

    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    protected function initArea()
    {
        try {
            $this->state->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        } catch (\Exception $e) {
            $this->context->helperContext()->logger()->critical($e);
            throw $e;
        }
        
        return $this;
    }

    /**
     * @param $orderData
     *
     * @return $this
     */
    protected function registerIntegracommerceData($orderData)
    {
        $this->getQuote()->setIntegracommerceCode($this->arrayExtract($orderData, 'integracommerce_code'));
        $this->getQuote()->setIntegracommerceChannel($this->arrayExtract($orderData, 'integracommerce_channel'));
        $this->getQuote()->setIntegracommerceData($this->arrayExtract($orderData, 'integracommerce_data'));
        $this->getQuote()->setIntegracommerceInterest($this->arrayExtract($orderData, 'integracommerce_interest'));

        return $this;
    }


    /**
     * @param array $data
     *
     * @return $this
     */
    protected function registerDiscount(array $data)
    {
        $registry = $this->context->helperContext()->registryManager();

        $key = 'mycostum_integracommerce_discount_amount';
        if ($registry->registry($key)) {
            $registry->unregister($key);
        }
        
        $discount = (float) $this->arrayExtract($data, 'discount_amount');

        if (!$discount) {
            return $this;
        }
    
        $this->getQuote()->setData('integracommerce_discount_amount', $discount);

        return $this;
    }


    /**
     * @param array $data
     *
     * @return $this
     */
    protected function registerInterest(array $data)
    {
        $registry = $this->context->helperContext()->registryManager();

        $key = 'mycostum_integracommerce_interest';
        if ($registry->registry($key)) {
            $registry->unregister($key);
        }
    
        $interest = (float) $this->arrayExtract($data, 'interest');

        if (!$interest) {
            return $this;
        }
    
        $this->getQuote()->setData('integracommerce_interest', $interest);

        return $this;
    }


    /**
     * @param array $data
     *
     * @return $this
     */
    protected function merge(array $data = [])
    {
        $this->data = array_merge_recursive($this->data, $data);

        return $this;
    }


    /**
     * @param null|StoreInterface|int $store
     *
     * @return StoreInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStore($store = null)
    {
        if (empty($store)) {
            $store = null;
        }

        if (!$this->store) {
            $this->store = $this->context->helperContext()->storeManager()->getStore($store);
        }

        return $this->store;
    }
}
