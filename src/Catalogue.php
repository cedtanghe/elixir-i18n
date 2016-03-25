<?php

namespace Elixir\I18N;

use Elixir\Config\Loader\LoaderFactory;
use Elixir\I18N\Loader\MO;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Catalogue 
{
    /**
     * @return void
     */
    public static function addDefaultLoaders()
    {
        if (!isset(LoaderFactory::$factories['MO']))
        {
            LoaderFactory::$factories['MO'] = function($config, $options)
            {
                if (strstr($config, '.mo'))
                {
                    return new MO();
                }
                
                return null;
            };
        }
        
        if (!isset(LoaderFactory::$factories['CSV']))
        {
            LoaderFactory::$factories['CSV'] = function($config, $options)
            {
                if(strstr($config, '.csv'))
                {
                    // Todo
                }
                
                return null;
            };
        }
    }
    
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
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }
    
    /**
     * @return string
     */
    public function getLocale() 
    {
        return $this->locale;
    }
    
    /**
     * @return array
     */
    public function getDomains()
    {
        return array_unique(array_merge(array_keys($this->messages), array_keys($this->resources)));
    }

    /**
     * @param string $domain
     * @return boolean
     */
    public function isResourcesLoaded($domain = null)
    {
        if (null !== $domain && isset($this->resources[$domain]))
        {
            $domains = [$this->resources[$domain]];
        }
        else
        {
            $domains = array_values($this->resources);
        }
        
        foreach ($domains as $data)
        {
            if ($data['loaded'])
            {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * @param string $domain
     * @param boolean $withInfos
     * @return array
     */
    public function getResources($domain = null, $withInfos = false)
    {
        if (null === $domain && $withInfos)
        {
            return $this->resources;
        }
        
        if (null !== $domain && isset($this->resources[$domain]))
        {
            $domains = [$domain => $this->resources[$domain]];
        }
        else
        {
            $domains = $this->resources;
        }
        
        $result = [];
        
        foreach ($domains as $textdomain => $resources)
        {
            foreach ($resources as $data)
            {
                $result[$textdomain][] = $withInfos ? $data : $data['resource'];
            }
        }
        
        return null !== $domain ? $result[$domain] : $result;
    }
    
    /**
     * @param mixed $resource
     * @param string $domain
     */
    public function addResource($resource, $domain = I18NInterface::DEFAULT_TEXT_DOMAIN)
    {
        $this->resources[$domain][] = [
            'resource' => $resource,
            'loaded' => false
        ];
    }
    
    /**
     * @param string $domain
     * @param boolean $addMetadata
     */
    public function loadResources($domain = null, $addMetadata = false)
    {
        if ($this->isResourcesLoaded($domain))
        {
            return;
        }
        
        foreach ($this->resources as $textdomain => &$resources)
        {
            if (null === $domain || $textdomain === $domain)
            {
                foreach ($resources as &$data)
                {
                    if ($data['loaded'])
                    {
                        continue;
                    }

                    $this->loadResource($data['resource'], $textdomain, $addMetadata);
                    $data['loaded'] = true;
                }
                
                if ($textdomain === $domain)
                {
                    return;
                }
            }
        }
    }
    
    /**
     * @param mixed $resource
     * @param string $domain
     * @param boolean $addMetadata
     * @return array
     */
    public function loadResource($resource, $domain, $addMetadata = false)
    {
        if (is_callable($resource))
        {
            return call_user_func_array($resource, [$this]);
        }
        
        $loader = LoaderFactory::create($resource);
        $parsed = LoadParser::parse($loader->load($resource));
        
        foreach ($parsed['messages'] as $id => $translation)
        {
            $this->addMessage($id, $translation, $domain);
        }
        
        if ($addMetadata)
        {
            foreach ($parsed['metadata'] as $meta => $value)
            {
                $this->setMetadata($meta, $value);
            }
        }
        
        return $parsed;
    }
    
    /**
     * @param string $id
     * @return boolean
     */
    public function hasMessage($id, $domain = I18NInterface::DEFAULT_TEXT_DOMAIN)
    {
        if (isset($this->messages[$domain][$id]))
        {
            return true;
        }
        else if (!$this->isResourcesLoaded($domain) && isset($this->resources[$domain]))
        {
            foreach ($this->resources[$domain] as &$resources)
            {
                foreach ($resources as &$data)
                {
                    if ($data['loaded'])
                    {
                        continue;
                    }
                    
                    $this->loadResource($data['resource'], $domain);
                    $data['loaded'] = true;

                    if (isset($this->messages[$domain][$id]))
                    {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * @param string $id
     * @param string $domain
     * @param mixed $default
     * @return string|array
     */
    public function getMessage($id, $domain = I18NInterface::DEFAULT_TEXT_DOMAIN, $default = null)
    {
        return $this->hasMessage($id, $domain) ? $this->messages[$domain][$id] : (is_callable($default) ? call_user_func($default) : $default);
    }
    
    /**
     * @param string $id
     * @param string|array $translation
     *  @param string $domain
     */
    public function addMessage($id, $translation, $domain = I18NInterface::DEFAULT_TEXT_DOMAIN)
    {
        $this->messages[$domain][$id] = $translation;
    }
    
    /**
     * @param string $id
     * @param string $domain
     */
    public function removeMessage($id, $domain = I18NInterface::DEFAULT_TEXT_DOMAIN)
    {
        unset($this->messages[$domain][$id]);
    }
    
    /**
     * @param string $domain
     * @return array
     */
    public function getMessages($domain = null)
    {
        return $domain ? (isset($this->messages[$domain]) ? $this->messages[$domain] : []) : $this->messages;
    }
    
    /**
     * @param array $messages
     * @param string $domain
     */
    public function setMessages(array $messages, $domain = null)
    {
        if (null === $domain)
        {
            $this->messages = $messages;
        }
        else
        {
            $this->messages[$domain] = $messages;
        }
    }
    
    /**
     * @param string $key
     * @return boolean
     */
    public function hasMetadata($key)
    {
        return isset($this->metadata[$key]);
    }
    
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMetadata($key, $default = null)
    {
        return isset($this->metadata[$key]) ? $this->metadata[$key] : (is_callable($default) ? call_user_func($default) : $default);
    }
    
    /**
     * @param string $key
     * @param mixed $value
     */
    public function setMetadata($key, $value)
    {
        $this->metadata[$key] = $value;
    }
    
    /**
     * @param string $key
     */
    public function removeMetadata($key)
    {
        unset($this->metadata[$key]);
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
     * @param Catalogue $catalogue
     */
    public function merge(Catalogue $catalogue) 
    {
        $messages = $catalogue->getMessages(null);
        $resources = $catalogue->getResources(null, true);
        $metadata = $catalogue->allMetadata();

        $data = [
            'messages' => $messages,
            'resources' => [],
            'metadata' => $metadata
        ];

        foreach ($resources as $textdomain => $list)
        {
            foreach ($list as $d)
            {
                if ($d['loaded'])
                {
                    continue;
                }

                $data['resources'][$textdomain][] = $d['resource'];
            }
        }

        foreach ($data['messages'] as $textdomain => $list)
        {
            foreach ($list as $id => $translation)
            {
                $this->addMessage($id, $translation, $textdomain);
            }
        }
        
        foreach ($data['resources'] as $textdomain => $list)
        {
            foreach ($list as $resource)
            {
                $this->addResource($resource, $textdomain);
            }
        }
        
        $this->metadata = array_merge($this->metadata, $data['metadata']);
    }
    
    /**
     * @ignore
     */
    public function __debugInfo()
    {
        return [
            'locale' => $this->getLocale(),
            'messages' => $this->getMessages(),
            'metadata' => array_keys($this->allMetadata()),
            'has_resources' => $this->isResourcesLoaded(),
            'domains' => $this->getDomains()
        ];
    }
}
