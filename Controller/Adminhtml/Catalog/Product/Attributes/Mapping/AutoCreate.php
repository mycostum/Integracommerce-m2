<?php

/**
 * Proudly powered by Magentor CLI!
 * Version v0.1.0
 * Official Repository: http://github.com/tiagosampaio/magentor
 *
 * Access https://ajuda.integracommerce.com.br/hc/pt-br/requests/new for questions and other requests.
 */

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Catalog\Product\Attributes\Mapping;

use Mycostum\IntegraCommerce\Model\Catalog\Product\Attributes\Mapping;

class AutoCreate extends AbstractMapping
{
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mycostum_IntegraCommerce::integracommerce_product_attributes_mapping_save';

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     *
     * @throws \Exception
     */
    public function execute()
    {
        $mappingId = $this->getRequest()->getParam('id');
    
        try {
            /** @var Mapping $mapping */
            $mapping = $this->productAttributeMappingRepository->get($mappingId);
            $mapping->setData('attribute_id', null);
        } catch (\Exception $e) {
            return $this->redirectIndex();
        }
    
        $attribute = $this->loadProductAttribute($mapping->getIntegracommerceCode());
    
        if ($attribute) {
            $mapping->setAttributeId((int) $attribute->getId());
        
            $this->messageManager
                ->addWarningMessage(__('There was already an attribute with the code "%1".', $mapping->getIntegracommerceCode()))
                ->addSuccessMessage(__('The attribute was only mapped automatically.'));
        }
        
        if (!$attribute) {
            $config = [
                'label'           => $mapping->getIntegracommerceLabel(),
                'type'            => 'varchar',
                'input'           => 'text',
                'required'        => 0,
                'visible_on_front'=> 0,
                'filterable'      => 0,
                'searchable'      => 0,
                'comparable'      => 0,
                'user_defined'    => 1,
                'is_configurable' => 0,
                'global'          => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'note'            => sprintf(
                    '%s. %s.',
                    'Created automatically by BSeller IntegraCommerce module.',
                    $mapping->getIntegracommerceDescription()
                ),
            ];
    
            $installConfig = (array) $mapping->getAttributeInstallConfig();
    
            foreach ($installConfig as $configKey => $itemValue) {
                if (is_null($itemValue)) {
                    continue;
                }
        
                $config[$configKey] = $itemValue;
            }
            
            /** @var \Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute $helper */
            $helper = $this->_objectManager
                ->create(\Mycostum\IntegraCommerce\Helper\Catalog\Product\Attribute::class);
            
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $helper->createProductAttribute($mapping->getIntegracommerceCode(), (array) $config);
    
            if (!$attribute || !$attribute->getId()) {
                $this->messageManager->addErrorMessage(__('There was a problem when trying to create the attribute.'));
                return $this->redirectIndex();
            }
    
            $mapping->setAttributeId((int) $attribute->getId());
        }
        
        $this->productAttributeMappingRepository->save($mapping);
    
        $message = __(
            'The attribute "%1" was created in Magento and associated to IntegraCommerce attribute "%2" automatically.',
            $attribute->getAttributeCode(),
            $mapping->getIntegracommerceCode()
        );
        
        $this->messageManager->addSuccessMessage($message);
        
        return $this->redirectIndex();
    }
    
    /**
     * @param string $code
     *
     * @return \Magento\Eav\Model\Entity\Attribute|null
     */
    protected function loadProductAttribute($code)
    {
        /** @var \Magento\Eav\Model\AttributeRepository $repository */
        $repository = $this->_objectManager->create(\Magento\Eav\Model\AttributeRepository::class);
        
        try {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $repository->get(\Magento\Catalog\Model\Product::ENTITY, $code);
        } catch (\Exception $e) {
            return null;
        }
        
        return $attribute;
    }
}
