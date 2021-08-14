<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Setup;

use Mycostum\IntegraCommerce\Functions;
use Mycostum\IntegraCommerce\Model\Catalog\Product\Attributes\Mapping;
use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Mycostum\IntegraCommerce\Model\Config\IntegracommerceAttributes\Data as IntegracommerceConfigData;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;

class InstallData implements InstallDataInterface
{
    
    use Functions, Setup;
    
    /** @var IntegracommerceConfigData */
    protected $integracommerceConfigData;
    
    /** @var AttributeFactory */
    protected $attributeFactory;
    
    
    /**
     * InstallData constructor.
     *
     * @param IntegracommerceConfigData $configData
     */
    public function __construct(IntegracommerceConfigData $configData, AttributeFactory $attributeFactory)
    {
        $this->integracommerceConfigData = $configData;
        $this->attributeFactory = $attributeFactory;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->setup()->startSetup();
    
        /**
         * Install bseller_integracommerce_product_attributes_mapping data.
         */
        $this->installCatalogProductIntegraCommerceRequiredAttributes();
        $this->createAssociatedSalesOrderStatuses($this->getStatuses());
    
        $this->setup()->endSetup();
    }
    
    
    /**
     * Install IntegraCommerce required attributes.
     *
     * @return $this
     */
    protected function installCatalogProductIntegraCommerceRequiredAttributes()
    {
        $attributes = (array)$this->integracommerceConfigData->getEntityAttributes(\Magento\Catalog\Model\Product::ENTITY);
        $table      = (string) $this->getTable('mycostum_integracommerce_product_attributes_mapping');
    
        /** @var array $attribute */
        foreach ($attributes as $identifier => $data) {
            $integracommerceCode  = $this->arrayExtract($data, 'code');
            $label       = $this->arrayExtract($data, 'label');
            $castType    = $this->arrayExtract($data, 'cast_type', Mapping::DATA_TYPE_STRING);
            $description = $this->arrayExtract($data, 'description');
            $validation  = $this->arrayExtract($data, 'validation');
            $enabled     = (bool) $this->arrayExtract($data, 'required', true);
            $required    = (bool) $this->arrayExtract($data, 'required', true);
            $editable    = (bool) $this->arrayExtract($data, 'editable', true);
            $createdAt   = $this->now();
        
            if (empty($integracommerceCode) || empty($castType)) {
                continue;
            }
        
            $attributeData = [
                'integracommerce_code'        => $integracommerceCode,
                'integracommerce_label'       => $label,
                'integracommerce_description' => $description,
                'enabled'            => $enabled,
                'cast_type'          => $castType,
                'validation'         => $validation,
                'required'           => $required,
                'editable'           => $editable,
                'created_at'         => $createdAt,
            ];
        
            $installConfig = (array) $this->arrayExtract($data, 'attribute_install_config', []);
            $magentoCode   = $this->arrayExtract($installConfig, 'attribute_code');
        
            /** @var int $attributeId */
            if ($attributeId = (int) $this->getAttributeIdByCode($magentoCode)) {
                $attributeData['attribute_id'] = $attributeId;
            }
    
            $this->getConnection()->beginTransaction();
        
            try {
                /** @var \Magento\Framework\DB\Select $select */
                $select = $this->getConnection()
                    ->select()
                    ->from($table, 'id')
                    ->where('integracommerce_code = :integracommerce_code')
                    ->limit(1);
            
                $id = $this->getConnection()->fetchOne($select, [':integracommerce_code' => $integracommerceCode]);
            
                if ($id) {
                    $this->getConnection()->update($table, $attributeData, "id = {$id}");
                    $this->getConnection()->commit();
                    continue;
                }
    
                $this->getConnection()->insert($table, $attributeData);
                $this->getConnection()->commit();
            } catch (\Exception $e) {
                $this->getConnection()->rollBack();
            }
        }
        
        return $this;
    }
    
    
    /**
     * @param array $states
     *
     * @return $this
     */
    public function createAssociatedSalesOrderStatuses(array $states = [])
    {
        foreach ($states as $stateCode => $statuses) {
            $this->createSalesOrderStatus($stateCode, $statuses);
        }
        
        return $this;
    }
    
    
    /**
     * @param string $state
     * @param array  $status
     *
     * @return $this
     */
    public function createSalesOrderStatus($state, array $status)
    {
        foreach ($status as $statusCode => $statusLabel) {
            $statusData = [
                'status' => $statusCode,
                'label'  => $statusLabel
            ];
            
            $this->getConnection()->insertOnDuplicate($this->getSalesOrderStatusTable(), $statusData, [
                'status', 'label'
            ]);
            
            $this->associateStatusToState($state, $statusCode);
        }
        
        return $this;
    }
    
    
    /**
     * @param string $state
     * @param string $status
     * @param int    $isDefault
     *
     * @return $this
     */
    public function associateStatusToState($state, $status, $isDefault = 0)
    {
        $associationData = [
            'status'     => (string) $status,
            'state'      => (string) $state,
            'is_default' => (int)    $isDefault,
        ];
        
        $this->getConnection()
            ->insertOnDuplicate($this->getSalesOrderStatusStateTable(), $associationData, [
                'status',
                'state',
                'is_default',
            ]);
        
        return $this;
    }
    
    
    /**
     * @param $code
     *
     * @return int|null
     */
    protected function getAttributeIdByCode($code)
    {
        $attributeId = null;
        
        try {
            /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $attribute */
            $attribute   = $this->attributeFactory->create();
            $attributeId = $attribute->getIdByCode(Product::ENTITY, $code);
        } catch (\Exception $e) {
        }
        
        return $attributeId;
    }
    
    
    /**
     * @return array
     */
    protected function getStatuses()
    {
        $statuses = [
            Order::STATE_COMPLETE => [
                'customer_delivered' => 'Delivered to Customer',
                'shipment_exception' => 'Shipment Exception',
            ]
        ];
        
        return $statuses;
    }
    
    
    /**
     * @return string
     */
    protected function getSalesOrderStatusTable()
    {
        return $this->getTable('sales_order_status');
    }
    
    
    /**
     * @return string
     */
    protected function getSalesOrderStatusStateTable()
    {
        return $this->getTable('sales_order_status_state');
    }
}
