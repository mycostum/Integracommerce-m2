<?php

namespace Mycostum\IntegraCommerce\Api;

interface OrderManagementInterface
{
    /**
     * @param int $entityId
     * @param string $invoiceKey
     *
     * @return bool
     */
    public function invoice($entityId, $invoiceKey);
}
