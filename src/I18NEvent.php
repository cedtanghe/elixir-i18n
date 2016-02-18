<?php

namespace Elixir\I18N;

use Elixir\Dispatcher\Event;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class I18NEvent extends Event 
{
    /**
     * @var string
     */
    const MISSING_TRANSLATION = 'missing_translation';
    
    /**
     * @var string
     */
    const MISSING_PLURAL_TRANSLATION = 'missing_plural_translation';
    
    /**
     * @var string|array
     */
    protected $message;
    
    /**
     * @var integer
     */
    protected $count;
    
    /**
     * @var string
     */
    protected $domain;
    
    /**
     * @var string
     */
    protected $locale;
    
    /**
     * {@inheritdoc}
     * @param array $params
     */
    public function __construct($pType, array $params = [])
    {
        parent::__construct($pType);
        
        $params += [
            'message' => null,
            'count' => 0,
            'domain' => null,
            'locale' => null
        ];
        
        $this->message = $params['message'];
        $this->count = $params['count'];
        $this->domain = $params['domain'];
        $this->locale = $params['locale'];
    }

    /**
     * @return string|array
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * @param string|array $value
     */
    public function setMessage($value)
    {
        $this->message = $value;
    }
    
    /**
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }
    
    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
    
    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
