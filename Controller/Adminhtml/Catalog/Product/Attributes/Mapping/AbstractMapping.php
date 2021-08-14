<?php

namespace Mycostum\IntegraCommerce\Controller\Adminhtml\Catalog\Product\Attributes\Mapping;

use Mycostum\IntegraCommerce\Api\ProductAttributeMappingRepositoryInterface;
use Mycostum\IntegraCommerce\Controller\Adminhtml\AbstractController;
use Mycostum\IntegraCommerce\Helper\Context as HelperContext;
use Magento\Backend\App\Action\Context;

abstract class AbstractMapping extends AbstractController
{
    
    /** @var ProductAttributeMappingRepositoryInterface */
    protected $productAttributeMappingRepository;

    /**
     * AbstractMapping constructor.
     *
     * @param Context                                    $context
     * @param HelperContext                              $helperContext
     * @param ProductAttributeMappingRepositoryInterface $productAttributeMappingRepository
     */
    public function __construct(
        Context $context,
        HelperContext $helperContext,
        ProductAttributeMappingRepositoryInterface $productAttributeMappingRepository
    ) {
        parent::__construct($context, $helperContext);
        $this->productAttributeMappingRepository = $productAttributeMappingRepository;
    }
    
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function redirectIndex()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $redirectPage */
        $redirectPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $redirectPage->setPath('*/*/index');
        
        return $redirectPage;
    }
}
