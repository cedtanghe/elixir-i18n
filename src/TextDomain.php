<?php

namespace Elixir\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class TextDomain 
{
    /**
     * @var string
     */
    protected $domain;
    
    /**
     * @var string
     */
    protected $locale;
    
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
     * @param string $domain
     * @param string $locale
     */
    public function __construct($domain, $locale)
    {
        $this->domain = $domain;
        $this->locale = $locale;
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
    
    /**
     * @return boolean
     */
    public function isResourcesLoaded()
    {
        foreach ($this->resources as &$data)
        {
            if ($data['loaded'])
            {
                return false;
            }
        }
        
        return true;
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
     * @param callable|string $resource
     */
    public function addResource($resource)
    {
        $this->resources[] = [
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
        
        foreach ($this->resources as &$data)
        {
            if ($data['loaded'])
            {
                continue;
            }

            $this->loadResource($data['resource']);
            $data['loaded'] = true;
        }
    }
    
    /**
     * @param string $id
     * @return boolean
     */
    public function hasMessage($id)
    {
        if (isset($this->messages[$id]))
        {
            return true;
        }
        else if (!$this->isResourcesLoaded())
        {
            foreach ($this->resources as &$data)
            {
                if ($data['loaded'])
                {
                    continue;
                }
                
                $this->loadResource($data['resource']);
                $data['loaded'] = true;
                
                if (isset($this->messages[$id]))
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
        return $this->hasMessage($id) ? $this->messages[$id] : (is_callable($default) ? call_user_func($default) : $default);
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
     * @param string $meta
     * @return boolean
     */
    public function hasMetadata($meta)
    {
        return isset($this->metadata[$meta]);
    }
    
    /**
     * @param string $meta
     * @param mixed $default
     * @return mixed
     */
    public function getMetadata($meta, $default = null)
    {
        return isset($this->metadata[$meta]) ? $this->metadata[$meta] : (is_callable($default) ? call_user_func($default) : $default);
    }
    
    /**
     * @param string $meta
     * @param mixed $value
     */
    public function setMetadata($meta, $value)
    {
        $this->metadata[$meta] = $value;
    }
    
    /**
     * @param string $meta
     */
    public function removeMetadata($meta)
    {
        unset($this->metadata[$meta]);
    }

    /**
     * @return array
     */
    public function allMetadata()
    {
        return $this->metadata;
    }
    
    /**
     * @param array $metadata
     */
    public function replaceMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }
    
    /**
     * @param string|callable $resource
     */
    protected function loadResource($resource)
    {
        // Todo
    }
    
    /**
     * @ignore
     */
    public function __debugInfo()
    {
        return [
            'locale' => $this->getLocale(),
            'domain' => $this->getDomain(),
            'messages' => $this->getMessages(),
            'metadata' => $this->allMetadata(),
            'resources' => $this->getResources()
        ];
    }
}
