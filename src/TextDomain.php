<?php

namespace Elixir\I18N;

use Elixir\I18N\Resource\ResourceInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class TextDomain 
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var callable
     */
    protected $pluralRule;
    
    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected $resources = [];

    /**
     * @param string $locale
     * @param callable $pluralRule
     * @throws \InvalidArgumentException
     */
    public function __construct($locale, callable $pluralRule = null)
    {
        $this->locale = $locale;
        $this->pluralRule = $pluralRule ?: PluralForms::get($this->locale)['plural'];
    }
    
    /**
     * @return string
     */
    public function getLocale() 
    {
        return $this->locale;
    }
    
    /**
     * @return callable
     */
    public function getPluralRule() 
    {
        return $this->pluralRule;
    }
    
    /**
     * @return boolean
     */
    public static function isResourcesLoaded()
    {
        return !in_array(false, array_values($this->resources));
    }
    
    /**
     * @param string $id
     * @param string $textDomain
     * @return boolean
     */
    public function hasMessage($id, $textDomain)
    {
        if (isset($this->messages[$textDomain][$id]))
        {
            return true;
        }
        else if (!$this->isResourcesLoaded())
        {
            foreach ($this->resources as $resource => &$data)
            {
                if ($data['loaded'])
                {
                    continue;
                }
                
                $this->loadResource($resource);
                $data['loaded'] = true;
                
                if (isset($this->messages[$textDomain][$id]))
                {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * @param string $id
     * @param mixed $default
     * @return string|array
     */
    public function getMessage($id, $default = null)
    {
        return $this->hasMessage($id) ? $this->messages[$id] : call_user_func($default);
    }
    
    /**
     * @param string $id
     * @param string|array $translation
     */
    public function addMessage($id, $translation)
    {
        $this->messages[$id] = $translation;
    }
    
    /**
     * @param string $id
     */
    public function removeMessage($id)
    {
        unset($this->messages[$id]);
    }
    
    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
    
    /**
     * @param array $messages
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }
    
    /**
     * @return array
     */
    public function getResources()
    {
        $resources = [];
        
        foreach ($this->resources as $data)
        {
            $resources[] = $data['resource'];
        }
        
        return $resources;
    }
    
    /**
     * @param \Elixir\I18N\ResourceInterface $resource
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[$resource->__toString()] = [
            'resource' => $resource,
            'loaded' => false
        ];
    }
    
    /**
     * @return void
     */
    public function loadResources()
    {
        if ($this->isResourcesLoaded())
        {
            return;
        }
        
        foreach ($this->resources as $resource => &$data)
        {
            if ($data['loaded'])
            {
                continue;
            }

            $this->loadResource($resource);
            $data['loaded'] = true;
        }
    }
    
    protected function loadResource($resource)
    {
        // Todo
    }
}
