<?php

namespace Mycostum\IntegraCommerce\Model\ResourceModel\Sales\Order;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

/**
 * Class Relation
 */
class Relation implements RelationInterface
{

    /** @var ExtensionAttribute */
    protected $integracommerceExtensionAttribute;


    /**
     * Relation constructor.
     *
     * @param \Mycostum\IntegraCommerce\Support\Order\ExtensionAttribute\Proxy $extensionAttribute
     */
    public function __construct(
        \Mycostum\IntegraCommerce\Support\Order\ExtensionAttribute\Proxy $extensionAttribute
    ) {
        $this->integracommerceExtensionAttribute = $extensionAttribute;
    }


    /**
     * Save relations for Order (IntegraCommerce data)
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     * @throws \Exception
     */
    public function processRelation(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->integracommerceExtensionAttribute->save($object);
    }
}