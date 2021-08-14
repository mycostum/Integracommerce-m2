<?php

namespace Mycostum\IntegraCommerce\Integration\Integrator;

use Mycostum\IntegraCommerce\Integration\Context;

abstract class AbstractIntegrator implements IntegratorInterface
{
    
    /** @var Context */
    protected $context;
    
    /** @var string */
    protected $eventPrefix  = 'integracommerce_integrator';
    
    /** @var string */
    protected $eventType    = null;
    
    /** @var string */
    protected $eventMethod  = null;
    
    /** @var string */
    protected $eventSuffix  = null;
    
    /** @var array */
    protected $eventParams  = [];
    
    
    /**
     * AbstractIntegrator constructor.
     */
    public function __construct(Context $context)
    {
        $this->init();
        
        $this->context = $context;
    }
    
    
    /**
     * @todo Check if this is really useful here.
     *
     * @return $this
     */
    protected function init()
    {
        return $this;
    }
    
    
    /**
     * @return string
     */
    protected function getEventName()
    {
        return vsprintf('%s_%s_%s_$s', [
            $this->eventPrefix,
            $this->eventType,
            $this->eventMethod,
            $this->eventSuffix,
        ]);
    }
    
    
    /**
     * @return $this
     */
    protected function resetEvent()
    {
        $this->eventType   = null;
        $this->eventMethod = null;
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    protected function beforeIntegration()
    {
        $this->resetEvent();
        
        $this->eventSuffix = 'before';
        $this->context->eventManager()->dispatch($this->getEventName(), (array) $this->eventParams);
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    protected function afterIntegration()
    {
        $this->eventSuffix = 'after';
        $this->context->eventManager()->dispatch($this->getEventName(), (array) $this->eventParams);
        
        return $this;
    }
    
    
    /**
     * @return \IntegraCommerce\Api
     */
    protected function api($new = false)
    {
        return $this->context->service((bool) $new)->api();
    }
}
