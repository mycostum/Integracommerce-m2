<?php

namespace Mycostum\IntegraCommerce\Cron\Queue;

use Mycostum\IntegraCommerce\Cron\AbstractCron;

abstract class AbstractQueue extends AbstractCron implements QueueInterface
{
    
    /** @var \Mycostum\IntegraCommerce\Model\ResourceModel\QueueFactory */
    protected $queueResourceFactory;

    public function __construct(
        \Mycostum\IntegraCommerce\Cron\Context $context,
        \Mycostum\IntegraCommerce\StoreConfig\Context $configContext,
        \Mycostum\IntegraCommerce\Model\StoreIteratorInterface $storeIterator,
        \Magento\Store\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\App\State $state,
        \Mycostum\IntegraCommerce\Model\ResourceModel\QueueFactory $queueResourceFactory
    ) {
        parent::__construct($context, $configContext, $storeIterator, $groupRepository, $state);
        $this->queueResourceFactory = $queueResourceFactory;
    }
    
    /**
     * @return \Mycostum\IntegraCommerce\Model\ResourceModel\Queue
     */
    public function getQueueResource()
    {
        return $this->queueResourceFactory->create();
    }
}
