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

class Unassociate extends AbstractMapping
{
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mycostum_IntegraCommerce::integracommerce_product_attributes_mapping_save';
    
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $mappingId = $this->getRequest()->getParam('id');
    
        /** @var Mapping $mapping */
        $mapping = $this->productAttributeMappingRepository->get($mappingId);
        $mapping->setData('attribute_id', null);
        
        $this->productAttributeMappingRepository->save($mapping);
        
        /** @var \Magento\Framework\Controller\Result\Redirect $redirectPage */
        $redirectPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
    
        $redirectPage->setPath('*/*/index');
        
        return $redirectPage;
    }
}
