<?php

namespace Mycostum\IntegraCommerce\Model\Config\Source\Integracommerce\Status;

use Mycostum\IntegraCommerce\Model\Config\Source\AbstractSource;

class Type extends AbstractSource
{

    const TYPE_NEW       = 'NEW';
    const TYPE_CANCELED  = 'CANCELED';
    const TYPE_APPROVED  = 'APPROVED';
    const TYPE_SHIPPED   = 'SHIPPED';
    const TYPE_DELIVERED = 'DELIVERED';


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::TYPE_NEW       => __('New'),
            self::TYPE_CANCELED  => __('Canceled'),
            self::TYPE_APPROVED  => __('Approved'),
            self::TYPE_SHIPPED   => __('Shipped'),
            self::TYPE_DELIVERED => __('Delivered'),
        ];
    }
}
