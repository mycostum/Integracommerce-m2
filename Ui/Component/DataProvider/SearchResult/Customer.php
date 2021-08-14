<?php

namespace Mycostum\IntegraCommerce\Ui\Component\DataProvider\SearchResult;

use Mycostum\IntegraCommerce\Ui\Component\DataProvider\SearchResult;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Customer extends SearchResult
{
    /** @var string */
    protected $entityType = null;


    /**
     * Queue constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger        $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager  $eventManager
     * @param string        $mainTable
     * @param string        $resourceModel
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $resourceModel = \Mycostum\IntegraCommerce\Model\ResourceModel\Customer\Attributes\Mapping::class,
        $mainTable = \Mycostum\IntegraCommerce\Model\ResourceModel\Customer\Attributes\Mapping::MAIN_TABLE,
        $identifierName = null,
        $connectionName = null,
        $entityType = null
    )
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName,
            $connectionName
        );

        if (!empty($entityType)) {
            $this->entityType = $entityType;
        }
    }
}
