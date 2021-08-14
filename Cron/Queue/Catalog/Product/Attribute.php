<?php

namespace Mycostum\IntegraCommerce\Cron\Queue\Catalog\Product;

use Mycostum\IntegraCommerce\Cron\Queue\AbstractQueue;
use Magento\Cron\Model\Schedule;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use IntegraCommerce\Api\Handler\Response\HandlerDefault;
use IntegraCommerce\Api\Handler\Response\HandlerException;

class Attribute extends AbstractQueue
{
    
    /**
     * @param Schedule $schedule
     */
    public function create(Schedule $schedule)
    {
        if (!$this->canRun($schedule)) {
            return;
        }
        
        $integrableIds = (array) array_keys($this->helper()->getIntegrableProductAttributes());
        
        try {
            $this->getQueueResource()->queue(
                $integrableIds,
                \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE,
                \Mycostum\IntegraCommerce\Model\Queue::PROCESS_TYPE_EXPORT
            );
            
            $message = __('Queue successfully created. IDs: %s.', implode(',', $integrableIds));
        } catch (\Exception $e) {
            $message = __('An has error has occurred when trying to queue the IDs: %s.', implode(',', $integrableIds));
        }
        
        $schedule->setMessages($message);
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
        
        $this->getQueueResource()->removeFromQueue(
            $successQueueIds,
            \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE
        );
        
        $message = __('All product attributes were successfully integrated.');
        
        if (!empty($failedQueueIds)) {
            $message .= " " . __('Some attributes could not be integrated.');
        }
        
        $schedule->setMessages($message);
    }

    /**
     * @param Schedule       $schedule
     * @param StoreInterface $store
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeIntegration(Schedule $schedule, StoreInterface $store)
    {
        if (!$this->canRun($schedule, $store->getId())) {
            return;
        }
        
        $attributeIds = (array) $this->getQueueResource()->getPendingEntityIds(
            \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE,
            \Mycostum\IntegraCommerce\Model\Queue::PROCESS_TYPE_EXPORT
        );
        
        if (empty($attributeIds)) {
            $schedule->setMessages(__('No product attribute to process.'));
            return;
        }
        
        $attributes = $this->helper()->getProductAttributes($attributeIds);
        
        $successQueueIds = [];
        $failedQueueIds  = [];
    
        /** @var \Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product\Attribute $integrator */
        $integrator = $this->createObject(\Mycostum\IntegraCommerce\Integration\Integrator\Catalog\Product\Attribute::class);
        
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($attributes as $attribute) {
            /** @var HandlerDefault|HandlerException $response */
            $response = $integrator->createOrUpdate($attribute);
            
            if ($response && $this->isErrorResponse($response)) {
                $failedQueueIds[] = $attribute->getId();
                
                $this->getQueueResource()->setFailedEntityIds(
                    $attribute->getId(),
                    \Mycostum\IntegraCommerce\Model\Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE,
                    $response->message()
                );
                
                continue;
            }
            
            $successQueueIds[$attribute->getId()] = $attribute->getId();
        }
        
        $this->mergeResults($schedule, $successQueueIds, $failedQueueIds);
    }

    /**
     * @return \Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute
     */
    protected function helper()
    {
        /** @var \Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute $helper */
        $helper = $this->createObject(\Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute::class);
        return $helper;
    }
    
    /**
     * @param Schedule $schedule
     * @param int|null $scopeCode
     *
     * @return bool
     */
    protected function canRun(Schedule $schedule, $scopeCode = null)
    {
        if (!$this->cronConfig()->catalogProductAttribute()->isEnabled($scopeCode)) {
            $schedule->setMessages(__('Catalog Product Attribute Cron is Disabled'));
            return false;
        }
        
        return parent::canRun($schedule, $scopeCode);
    }
}
