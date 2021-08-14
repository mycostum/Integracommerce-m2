<?php

namespace Mycostum\IntegraCommerce\Cron\Queue\Catalog;

use Mycostum\IntegraCommerce\Cron\Queue\AbstractQueue;
use Magento\Cron\Model\Schedule;
use Magento\Store\Api\Data\StoreInterface;

class Product extends AbstractQueue
{
    
    /**
     * @param Schedule $schedule
     *
     * @return mixed|void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(Schedule $schedule)
    {
        if (!$this->canRun($schedule)) {
            return;
        }
    
        /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\Entity $entityResource */
        $entityResource = $this->createObject(\Mycostum\IntegraCommerce\Model\ResourceModel\Entity::class);
        
        $queuedIds = (array) $this->getQueueResource()->getPendingEntityIds(
            \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT,
            \Mycostum\IntegraCommerce\Model\Queue::PROCESS_TYPE_EXPORT
        );
        
        $queuedIds          = $this->filterIds($queuedIds);
        $integracommerceEntityTable  = $entityResource->getMainTable();
        
        /** @var array $productVisibilities */
        $productVisibilities = $this->configContext->catalog()->getProductVisibilities();
        
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->getProductCollection()
            ->addAttributeToFilter('visibility', ['in' => $productVisibilities]);
        
        if (!empty($queuedIds)) {
            $collection->addFieldToFilter('entity_id', ['nin' => $queuedIds]);
        }
        
        /** @var \Magento\Framework\DB\Select $select */
        $select = $collection->getSelect()
            ->joinLeft(
                ['bseller_integracommerce_entity' => $integracommerceEntityTable],
                "bseller_integracommerce_entity.entity_id = e.entity_id
                 AND bseller_integracommerce_entity.entity_type = '".\Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT."'"
            )
            ->reset('columns')
            ->columns('e.entity_id')
            ->where('bseller_integracommerce_entity.updated_at IS NULL OR e.updated_at >= bseller_integracommerce_entity.updated_at')
            ->order(['e.updated_at DESC', 'e.created_at DESC']);
        
        /** Set limitation. */
        $limit = abs($this->cronConfig()->catalogProduct()->getQueueCreateLimit());
        
        if ($limit) {
            $select->limit((int) $limit);
        }
        
        $productIds = (array) $this->getQueueResource()->getConnection()->fetchCol($select);
        
        if (empty($productIds)) {
            $schedule->setMessages(__('No products to be queued this time.'));
            return;
        }
        
        $this->getQueueResource()
            ->queue(
                $productIds,
                \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT,
                \Mycostum\IntegraCommerce\Model\Queue::PROCESS_TYPE_EXPORT
            );
        
        $schedule->setMessages(
            __('%s product(s) were queued. IDs: %s.', count($productIds), implode(',', $productIds))
        );
    }

    /**
     * @param Schedule $schedule
     *
     * @return mixed|void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Schedule $schedule)
    {
        $this->processStoreIteration($this, 'executeIntegration', $schedule);
        
        $successQueueIds = $this->extractResultSuccessIds($schedule);
        $failedQueueIds  = $this->extractResultFailIds($schedule);
        
        if (!empty($successQueueIds)) {
            $this->getQueueResource()->removeFromQueue(
                $successQueueIds,
                \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT
            );
        }
        
        $schedule->setMessages(__(
            'Queue was processed. Success: %s. Errors: %s.',
            implode(',', $successQueueIds),
            implode(',', $failedQueueIds)
        ));
    }
    
    /**
     * @param Schedule       $schedule
     * @param StoreInterface $store
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function executeIntegration(Schedule $schedule, StoreInterface $store)
    {
        if (!$this->canRun($schedule)) {
            return;
        }
        
        $productIds = (array) $this->getQueueResource()->getPendingEntityIds(
            \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT,
            \Mycostum\IntegraCommerce\Model\Queue::PROCESS_TYPE_EXPORT
        );
        
        $productIds = $this->filterIds($productIds);
        
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->getProductCollection()
            ->addStoreFilter($store)
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addMediaGalleryData();
        
        /** Set limitation. */
        $limit = abs($this->cronConfig()->catalogProduct()->getQueueExecuteLimit());
        
        if ($limit) {
            $collection->getSelect()->limit((int) $limit);
        }
        
        if (!$collection->getSize()) {
            $schedule->setMessages(__('No product to be integrated this time.'));
            return;
        }
        
        $successQueueIds = [];
        $failedQueueIds  = [];
        
        /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product $integrator */
        $integrator = $this->createObject(\Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product::class);
        
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        foreach ($collection as $product) {
            /** @var \IntegraCommerce\Api\Handler\Response\HandlerInterface $response */
            $response = $integrator->createOrUpdate($product);
            
            /*
             * If the response is exactly equal to false, means it cannot be integrated because of internal validation;
             */
            if ($response === false) {
                $this->getQueueResource()->removeFromQueue(
                    [$product->getId()],
                    \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT
                );
                continue;
            }
            
            if ($this->isErrorResponse($response)) {
                $failedQueueIds[] = $product->getId();
                
                /** @var \IntegraCommerce\Api\Handler\Response\HandlerException $response */
                $this->getQueueResource()->setFailedEntityIds(
                    $product->getId(),
                    \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT,
                    $response->message()
                );
                
                continue;
            }
            
            $successQueueIds[] = $product->getId();
        }
        
        $this->mergeResults($schedule, $successQueueIds, $failedQueueIds);
    }
    
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->createObject(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        return $collection;
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    protected function filterIds(array $ids)
    {
        $ids = array_filter($ids, function (&$value) {
            $value = (int) $value;
            return $value;
        });
        
        return (array) $ids;
    }
    
    /**
     * @param Schedule $schedule
     * @param int|null $storeId
     *
     * @return bool
     */
    protected function canRun(Schedule $schedule, $storeId = null)
    {
        if (!$this->cronConfig()->catalogProduct()->isEnabled($storeId)) {
            $schedule->setMessages(__('Catalog Product Cron is Disabled'));
            return false;
        }
        
        /** @var \Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute\Mapping $helper */
        $helper = $this->createObject(\Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute\Mapping::class);
        
        /**
         * If the notification block can be shown, it means there's a products attributes mapping problem.
         */
        if ($helper->hasPendingAttributesForMapping()) {
            $schedule->setMessages(
                __('The installation is not completed. All required product attributes must be mapped.')
            );
            return false;
        }
        
        return parent::canRun($schedule, $storeId);
    }
}
