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
    protected $number;
    
    /**
     * @var array
     */
    protected $options;
    
    /**
     * {@inheritdoc}
     * @param array $params
     */
    public function __construct($type, array $params = [])
    {
        parent::__construct($type);
        
        $params += [
            'message' => null,
            'number' => 0,
            'options' => []
        ];
        
        $this->message = $params['message'];
        $this->number = $params['number'];
        $this->options = $params['options'];
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
    public function getNumber()
    {
        return $this->number;
    }
    
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
