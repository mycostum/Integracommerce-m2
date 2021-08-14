<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Model\ResourceModel;

class Order extends AbstractResourceModel
{
    
    /** @var string */
    const MAIN_TABLE = 'mycostum_integracommerce_orders';
    
    /** @var string */
    const ID_FIELD   = 'id';
    
    
    /**
     * Initialize database relation.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD);
    }
    
    
    /**
     * @param string   $integracommerceCode
     * @param int|null $storeId
     *
     * @return bool|int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderId($integracommerceCode, $storeId = null)
    {
        /** @var \Magento\Framework\DB\Select $select */
        $select = $this->getConnection()
            ->select()
            ->from($this->getMainTable(), 'order_id')
            ->where('code = ?', $integracommerceCode)
            ->where('store_id IN (?)', $this->getDefaultStoreIdsFilter($storeId))
            ->limit(1);
        
        $result = $this->getConnection()->fetchOne($select);
        
        if (!$result) {
            return false;
        }
        
        return (int) $result;
    }
    
    
    /**
     * @param string   $integracommerceCode
     * @param null|int $storeId
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function orderExists($integracommerceCode, $storeId = null)
    {
        $orderId = $this->getOrderId($integracommerceCode, $storeId);
        return (bool) $orderId;
    }


    /**
     * @param null|int $storeId
     *
     * @return array
     */
    public function getDefaultStoreIdsFilter($storeId = null)
    {
        $storeIds = [0];

        if (empty($storeId)) {
            return $storeIds;
        }

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        $storeIds = array_merge($storeIds, $storeId);

        return $storeIds;
    }
    
    
    /**
     * @param string $incrementId
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityIdByIncrementId($incrementId)
    {
        /** @var \Magento\Framework\DB\Select $select */
        $select = $this->getConnection()
            ->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('increment_id = ?', $incrementId)
            ->limit(1);
        
        return $this->getConnection()->fetchOne($select);
    }
    
    
    /**
     * @param string $code
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityIdByIntegracommerceCode($code)
    {
        /** @var \Magento\Framework\DB\Select $select */
        $select = $this->getConnection()
            ->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('code = ?', $code)
            ->limit(1);
        
        return $this->getConnection()->fetchOne($select);
    }
    
    
    /**
     * @param int $orderId
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIntegracommerceCodeByOrderId($orderId)
    {
        /** @var \Magento\Framework\DB\Select $select */
        $select = $this->getConnection()
            ->select()
            ->from($this->getMainTable(), 'code')
            ->where('order_id = ?', $orderId)
            ->limit(1);
        
        return $this->getConnection()->fetchOne($select);
    }
}
