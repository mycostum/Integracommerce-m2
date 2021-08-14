<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Setup;

use Mycostum\IntegraCommerce\Model\Queue;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    
    use SetupHelper;
    
    
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        
        $this->setup()->startSetup();
        
        $this->installProductsAttributeMappingTable();
        $this->installEntityIdTable();
        $this->installQueueTable();
        $this->installOrdersTable();
        
        $this->setup()->endSetup();
    }
    
    
    /**
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     *
     * @throws \Zend_Db_Exception
     */
    protected function installProductsAttributeMappingTable()
    {
        $tableName = $this->getTable('mycostum_integracommerce_product_attributes_mapping');
        
        /** Drop the table first. */
        $this->getConnection()->dropTable($tableName);
        
        /** @var Table $table */
        $table = $this->getConnection()
            ->newTable($tableName)
            ->addColumn('id', Table::TYPE_INTEGER, 10, [
                'identity' => true,
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
            ])
            ->addColumn('integracommerce_code', Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ])
            ->addColumn('integracommerce_label', Table::TYPE_TEXT, 255, [
                'nullable' => true,
            ])
            ->addColumn('integracommerce_description', Table::TYPE_TEXT, null, [
                'nullable' => true,
            ])
            ->addColumn('enabled', Table::TYPE_BOOLEAN, 1, [
                'nullable' => false,
                'default' => true,
            ])
            ->addColumn('cast_type', Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ])
            ->addColumn('validation', Table::TYPE_TEXT, null, [
                'nullable' => true,
            ])
            ->addColumn('attribute_id', Table::TYPE_SMALLINT, 5, [
                'unsigned' => true,
                'nullable' => true,
                'default' => null,
            ])
            ->addColumn('required', Table::TYPE_BOOLEAN, 1, [
                'nullable' => false,
                'default' => true,
            ])
            ->addColumn('editable', Table::TYPE_BOOLEAN, 1, [
                'nullable' => false,
                'default' => true,
            ])
            ->addColumn('created_at', Table::TYPE_DATETIME, null, [
                'nullable' => true,
                'unsigned' => true,
            ])
            ->addColumn('updated_at', Table::TYPE_DATETIME, null, [
                'nullable' => true,
                'unsigned' => true,
            ])
            ->setComment('IntegraCommerce Product Attributes Mapping.');
        ;
        
        /** Add Unique Index */
        $this->addTableIndex($table, ['integracommerce_code', 'attribute_id'], AdapterInterface::INDEX_TYPE_UNIQUE);
        
        /** Add Foreign Key */
        $this->addTableForeignKey($table, 'attribute_id', 'eav_attribute', 'attribute_id', Table::ACTION_SET_NULL);
        
        $this->getConnection()->createTable($table);
        
        return $this;
    }
    
    
    /**
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     *
     * @throws \Zend_Db_Exception
     */
    protected function installEntityIdTable()
    {
        $tableName = $this->getTable('mycostum_integracommerce_entity_id');
        
        /** Drop the table first. */
        $this->getConnection()->dropTable($tableName);
        
        /** @var Table $table */
        $table = $this->getConnection()
            ->newTable($tableName)
            ->addColumn('id', Table::TYPE_INTEGER, 10, [
                'identity' => true,
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
            ])
            ->addColumn('entity_id', Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'primary' => true,
            ])
            ->addColumn('entity_type', Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ])
            ->addColumn('store_id', Table::TYPE_SMALLINT, 5, [
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'default' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->addColumn('force_integration', Table::TYPE_BOOLEAN, 1, [
                'unsigned' => true,
                'default'  => false,
            ])
            ->addColumn('created_at', Table::TYPE_DATETIME, null, [
                'nullable' => true,
                'unsigned' => true,
            ])
            ->addColumn('updated_at', Table::TYPE_DATETIME, null, [
                'nullable' => true,
                'unsigned' => true,
            ]);
        
        /** Add Store ID foreign key. */
        $this->addTableForeignKey($table, 'store_id', 'store', 'store_id');
        
        /** Add indexes */
        $this->addTableIndex($table, 'entity_id')
            ->addTableIndex($table, 'entity_type')
            ->addTableIndex($table, ['entity_id', 'entity_type', 'store_id'], AdapterInterface::INDEX_TYPE_UNIQUE);
        
        $this->getConnection()->createTable($table);
        
        return $this;
    }
    
    
    /**
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     *
     * @throws \Zend_Db_Exception
     */
    protected function installQueueTable()
    {
        $tableName = $this->getTable('mycostum_integracommerce_queue');
        
        /** Drop the table first. */
        $this->getConnection()->dropTable($tableName);
        
        /** @var Table $table */
        $table = $this->getConnection()
            ->newTable($tableName)
            ->addColumn('id', Table::TYPE_INTEGER, 10, [
                'identity' => true,
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
            ])
            ->addColumn('entity_id', Table::TYPE_INTEGER, 10, [
                'nullable' => true,
            ])
            ->addColumn('reference', Table::TYPE_TEXT, 255, [
                'nullable' => true,
            ])
            ->addColumn('entity_type', Table::TYPE_TEXT, 255, [
                'nullable' => true,
            ])
            ->addColumn('status', Table::TYPE_INTEGER, 2, [
                'nullable' => false,
                'default' => 0,
            ])
            ->addColumn('process_type', Table::TYPE_INTEGER, 2, [
                'nullable' => false,
                'default' => Queue::PROCESS_TYPE_EXPORT,
            ])
            ->addColumn('messages', Table::TYPE_TEXT, null, [
                'nullable' => true,
            ])
            ->addColumn('additional_data', Table::TYPE_TEXT, null, [
                'nullable' => true,
            ])
            ->addColumn('can_process', Table::TYPE_INTEGER, 1, [
                'nullable' => false,
                'default' => 0,
            ])
            ->addColumn('store_id', Table::TYPE_SMALLINT, 5, [
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'default' => 0,
            ])
            ->addColumn('process_after', Table::TYPE_DATETIME, null, [
                'nullable' => true,
                'unsigned' => true,
            ], 'Schedule the process to run after this time if needed.')
            ->addColumn('created_at', Table::TYPE_DATETIME, null, [
                'nullable' => true,
                'unsigned' => true,
            ])
            ->addColumn('updated_at', Table::TYPE_DATETIME, null, [
                'nullable' => true,
                'unsigned' => true,
            ]);
        
        $this->addTableForeignKey($table, 'store_id', 'store', 'store_id');
        
        $this->addTableIndex($table, 'entity_id')
            ->addTableIndex($table, 'entity_type')
            ->addTableIndex($table, ['entity_id', 'entity_type', 'store_id'], AdapterInterface::INDEX_TYPE_UNIQUE);
        
        $this->getConnection()->createTable($table);
        
        return $this;
    }
    
    
    /**
     * @return $this
     *
     * @throws \Zend_Db_Exception
     */
    protected function installOrdersTable()
    {
        $tableName = $this->getTable(\Mycostum\IntegraCommerce\Model\ResourceModel\Order::MAIN_TABLE);
        
        /** Drop the table first. */
        $this->getConnection()->dropTable($tableName);
        
        /** @var Table $table */
        $table = $this->getConnection()
            ->newTable($tableName)
            ->addColumn(\Mycostum\IntegraCommerce\Model\ResourceModel\Order::ID_FIELD, Table::TYPE_INTEGER, 10, [
                'identity' => true,
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
            ], 'ID')
            ->addColumn('store_id', Table::TYPE_SMALLINT, 5, [
                'nullable' => false,
                'default' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                'unsigned' => true,
            ], 'Store Entity ID')
            ->addColumn('order_id', Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'unsigned' => true,
            ], 'Order Entity ID')
            ->addColumn('code', Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'IntegraCommerce Code')
            ->addColumn('channel', Table::TYPE_TEXT, 255, [
                'nullable' => true,
                'default' => null,
            ], 'IntegraCommerce Channel')
            ->addColumn('invoice_key', Table::TYPE_TEXT, 255, [
                'nullable' => true,
                'default' => null,
            ], 'IntegraCommerce Invoice Key')
            ->addColumn('interest', Table::TYPE_DECIMAL, '12,4', [
                'nullable' => false,
                'default' => '0.0000',
            ], 'IntegraCommerce Interest Amount')
            ->addColumn('data_source', Table::TYPE_TEXT, null, [
                'nullable' => true,
                'default' => null,
            ], 'IntegraCommerce Order JSON');
        
        /**
         * Add relations.
         */
        $this->addTableForeignKey($table, 'store_id', 'store', 'store_id');
        $this->addTableForeignKey($table, 'order_id', 'sales_order', 'entity_id');
        
        /**
         * Add unique index.
         */
        $this->addTableIndex($table, ['store_id', 'order_id', 'code'], AdapterInterface::INDEX_TYPE_UNIQUE);
        
        $this->getConnection()->createTable($table);
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    protected function updateTablesColumns()
    {
        foreach ($this->getUpdatableTables() as $tableName => $columns) {
            foreach ($columns as $columnName => $definition) {
                $this->setup->getConnection()
                    ->addColumn($this->setup->getTable($tableName), $columnName, $definition);
            }
        }
        
        return $this;
    }
    
    
    /**
     * @return array
     */
    protected function getUpdatableTables()
    {
        return [
            $this->setup->getTable('sales_quote') => [
                'bseller_integracommerce_interest' => $this->getInterestField()
            ],
            $this->setup->getTable('sales_order') => [
                'bseller_integracommerce' => [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'default' => false,
                    'comment' => 'If Order Was Created By BSeller IntegraCommerce',
                ],
                'bseller_integracommerce_code' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'default' => false,
                    'after' => 'bseller_integracommerce',
                    'comment' => 'IntegraCommerce Code',
                ],
                'bseller_integracommerce_channel' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'default' => false,
                    'after' => 'bseller_integracommerce_code',
                    'comment' => 'IntegraCommerce Code',
                ],
                'bseller_integracommerce_invoice_key' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'default' => null,
                    'after' => 'bseller_integracommerce_channel',
                    'comment' => 'IntegraCommerce Invoice Key',
                ],
                'bseller_integracommerce_interest' => array_merge($this->getInterestField(), [
                    'after' => 'bseller_integracommerce_invoice_key'
                ]),
            ]
        ];
    }
    
    
    /**
     * @return array
     */
    protected function getInterestField()
    {
        return [
            'type' => Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'IntegraCommerce Interest Amount',
        ];
    }
}
