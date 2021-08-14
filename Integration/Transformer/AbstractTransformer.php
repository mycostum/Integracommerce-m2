<?php

namespace Mycostum\IntegraCommerce\Integration\Transformer;

use Mycostum\IntegraCommerce\Functions;
use Mycostum\IntegraCommerce\Integration\Context;

abstract class AbstractTransformer implements TransformerInterface
{
    
    use Functions;
    
    
    /** @var Context */
    protected $context;
    
    
    /**
     * AbstractTransformer constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }
}
