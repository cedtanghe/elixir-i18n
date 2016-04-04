<?php

namespace Elixir\I18N;

use Elixir\Config\Cache\CacheableInterface;
use Elixir\Config\Loader\LoaderFactory;
use Elixir\Config\Loader\LoaderFactoryAwareTrait;
use Elixir\Config\Writer\WriterFactory;
use Elixir\Config\Writer\WriterInterface;
use Elixir\I18N\Loader\CSVLoader;
use Elixir\I18N\Loader\MOLoader;
use Elixir\I18N\Loader\POLoader;
use Elixir\I18N\LoadParser;
use Elixir\I18N\Writer\CSVWritter;
use Elixir\I18N\Writer\POWriter;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Catalogue implements CacheableInterface
{
    use LoaderFactoryAwareTrait;
    
    /**
     * @param LoaderFactory $factory
     */
    public static function addLoaderProvider(LoaderFactory $factory)
    {
        $factory->add('MO', function($config, $options)
        {
            if (strstr($config, '.mo'))
            {
                return new MOLoader();
            }
            
            return null;
        });
        
        $factory->add('CSV', function($config, $options)
        {
            if(strstr($config, '.csv'))
            {
                new CSVLoader();
            }
            
            return null;
        });
        
        $factory->add('PO', function($config, $options)
        {
            if (strstr($config, '.po'))
            {
                return new POLoader();
            }
            
            return null;
        });
    }
    
    /**
     * @param WriterFactory $factory
     */
    public static function addWriterProvider(WriterFactory $factory)
    {
        $factory->add('CSV', function($file, $options)
        {
            if (strstr($file, '.csv'))
            {
                return new CSVWritter();
            }
            
            return null;
        });
        
        $factory->add('PO', function($file, $options)
        {
            if (strstr($file, '.po'))
            {
                return new POWriter();
            }
            
            return null;
        });
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
     * @var CacheableInterface 
     */
    protected $cache;

    /**
     * @param string $locale
     * @param array $messages
     * @param array $metadata
     */
    public function __construct($locale, array $messages = [], array $metadata = [])
    {
        $this->locale = $locale;
        $this->messages = $messages;
        $this->metadata = $metadata;
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
     * @param CacheableInterface $value
     */
    public function setCacheStrategy(CacheableInterface $value)
    {
        $this->cache = $value;
    }
    
    /**
     * @return CacheableInterface
     */
    public function getCacheStrategy()
    {
        return $this->cache;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadCache()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        $data = $this->cache->loadCache();
        
        if ($data)
        {
            $data = LoadParser::parse($data);
            $this->messages = array_merge($this->messages, $data['messages']);
            $this->metadata = array_merge($this->metadata, $data['metadata']);
        }
        
        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function cacheLoaded()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        return $this->cache->cacheLoaded();
    }

    /**
     * @param string $domain
     * @return boolean
     */
    public function isResourcesLoaded($domain = null)
    {
        if (null !== $domain && isset($this->resources[$domain]))
        {
            $domains = $this->resources[$domain];
        }
        else
        {
            $domains = [];
            
            foreach ($this->resources as $domain => $resources)
            {
                $domains = array_merge($domains, $resources);
            }
        }
        
        foreach ($domains as $data)
        {
            if ($data['loaded'])
            {
                return true;
            }
        }
        
        return false;
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
     * @param array $options
     */
    public function addResource($resource, array $options = [])
    {
        if ($this->cacheLoaded() && $this->isFreshCache())
        {
            return true;
        }
        
        $domain = isset($options['domain']) ? $options['domain'] : I18NInterface::DEFAULT_TEXT_DOMAIN;
        unset($options['domain']);
        
        $this->resources[$domain][] = [
            'resource' => $resource,
            'options' => $options,
            'loaded' => false
        ];
    }
    
    /**
     * @param array $options
     */
    public function loadResources(array $options = [])
    {
        $domain = isset($options['domain']) ? $options['domain'] : null;
        unset($options['domain']);
        
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

                    $this->loadResource(
                        $data['resource'], 
                        $textdomain, 
                        $data['options'] + $options
                    );
                    
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
     * @param array $options
     * @return array
     */
    public function loadResource($resource, $domain, array $options = [])
    {
        if (is_callable($resource))
        {
            return call_user_func_array($resource, [$this]);
        }
        
        if (null === $this->loaderFactory)
        {
            $this->loaderFactory = new LoaderFactory();
            self::addLoaderProvider($this->loaderFactory);
        }
        
        $loader = $this->loaderFactory->create($resource, $options);
        $parsed = LoadParser::parse($loader->load($resource));
        
        foreach ($parsed['messages'] as $id => $translation)
        {
            $this->addMessage($id, $translation, $domain);
        }
        
        if (isset($options['load-metadata']) && $options['load-metadata'])
        {
            foreach ($parsed['metadata'] as $meta => $value)
            {
                if ($meta === 'options')
                {
                    continue;
                }
                
                $this->setMetadata($meta, $value);
            }
        }
        
        if (isset($parsed['metadata']['options']))
        {
            $options = $this->getMetadata('options', []);
            
            if (!isset($options[$domain]))
            {
                $options[$domain] = [];
            }
            
            $options[$domain] += $parsed['metadata']['options'];
            
            $this->setMetadata('options', $options);
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
     * @param WriterInterface $writer
     * @param string $file
     * @param string $domain
     * @return boolean
     */
    public function export(WriterInterface $writer, $file, $domain = I18NInterface::DEFAULT_TEXT_DOMAIN)
    {
        $this->loadResources(['domain' => $domain]);
        
        return $writer->export([
                'locale' => $this->getLocale(),
                'domain' => $domain,
                'messages' => $this->getMessages($domain),
                'metadata' => $this->allMetadata()
            ], 
            $file
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function isFreshCache()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        return $this->cache->isFreshCache();
    }
    
    /**
     * {@inheritdoc}
     */
    public function exportToCache(array $data = null)
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        if ($data)
        {
            $data = LoadParser::parse($data);
            $this->messages = array_merge($this->messages, $data['messages']);
            $this->metadata = array_merge($this->metadata, $data['metadata']);
        }
        
        return $this->cache->exportToCache($this->getExportableData());
    }
    
    /**
     * {@inheritdoc}
     */
    public function invalidateCache()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        return $this->cache->invalidateCache();
    }
    
    /**
     * @return array
     */
    protected function getExportableData()
    {
        $this->loadResources();
        
        return [
            'messages' => $this->messages,
            'metadata' => $this->metadata
        ];
    }
    
    /**
     * @param Catalogue $catalogue
     */
    public function merge(Catalogue $catalogue) 
    {
        $resources = $catalogue->getResources(null, true);

        foreach ($resources as &$group)
        {
            foreach ($group as $key => $value)
            {
                if ($v['loaded'])
                {
                    unset($group[$key]);
                }
            }
        }
        
        $this->resources = array_merge($this->resources, $resources);
        $this->messages = array_merge($this->messages, $catalogue->getMessages(null));
        $this->metadata = array_merge($this->metadata, $catalogue->allMetadata());
    }
    
    /**
     * @ignore
     */
    public function __debugInfo()
    {
        return [
            'locale' => $this->getLocale(),
            'messages' => $this->getMessages(),
            'metadata' => $this->allMetadata(),
            'has_resources' => $this->isResourcesLoaded(),
            'domains' => $this->getDomains()
        ];
    }
}
