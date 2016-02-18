<?php

namespace Elixir\I18N;

use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Dispatcher\DispatcherTrait;
use Elixir\I18N\Catalogue;
use Elixir\I18N\I18NInterface;
use Elixir\I18N\Locale;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class I18N implements I18NInterface, DispatcherInterface
{
    use DispatcherTrait;
    
    /**
     * @var string
     */
    protected $locale;
    
    /**
     * @var array 
     */
    protected $catalogues = [];
    
    /**
     * {@inheritdoc}
     */
    public function setLocale($value)
    {
        $this->locale = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        $this->locale = $this->locale ?: Locale::getDefault();
        return $this->locale;
    }
    
    /**
     * @param string $locale
     * @return boolean
     */
    public function hasCatalogue($locale)
    {
        return isset($this->catalogues[$locale]);
    }

    /**
     * @param string $locale
     * @param mixed $default
     * @return mixed;
     */
    public function getCatalogue($locale, $default = null)
    {
        if (!$this->hasCatalogue($locale))
        {
            return $this->catalogues[$locale];
        }
        
        return is_callable($default) ? call_user_func($default) : $default;
    }
    
    /**
     * @param Catalogue $catalogue
     * @param string $locale
     */
    public function addCatalogue(Catalogue $catalogue, $locale = null)
    {
        $locale = $locale ? ($catalogue->getLocale() ?: $this->getLocale()) : $this->getLocale();
        
        if ($this->hasCatalogue($locale))
        {
            $this->catalogues[$locale] = $catalogue;
        }
        else
        {
            $this->catalogues[$locale]->merge($catalogue);
        }
    }
    
    /**
     * @param string $locale
     */
    public function removeCatalogue($locale)
    {
        unset($this->catalogues[$locale]);
    }
    
    /**
     * @return array
     */
    public function getCatalogues()
    {
        return $this->catalogues;
    }
    
    /**
     * @return array
     */
    public function setCatalogues(array $catalogues)
    {
        $this->catalogues = [];
        
        foreach ($catalogues as $catalogue)
        {
            $this->addCatalogue($catalogue, $catalogue->getLocale());
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function translate($message, array $options = [])
    {
        $locale = isset($options['locale']) ? $options['locale'] : $this->getLocale();
        $domain = isset($options['domain']) ? $options['domain'] : self::DEFAULT_TEXT_DOMAIN;
        $catalogue = $this->getCatalogue($locale);
        
        if (!$catalogue)
        {
            $translated = null;
        }
        else
        {
            $translated = $catalogue->getMessage($message, $domain);
        }
        
        if (!$translated)
        {
            $event = new I18NEvent(I18NEvent::MISSING_TRANSLATION, ['message' => $message, 'domain' => $domain, 'locale' => $locale]);
            $this->dispatch($event);
            
            $translated = $event->getMessage() ?: $message;
        }
        
        if (isset($options['%']))
        {
            $translated = str_replace(array_keys($options['%']), array_values($options['%']), $translated);
        }
        
        return $translated;
    }
    
    /**
     * {@inheritdoc}
     */
    public function transPlural($singular, $plural, $count, array $options = [])
    {
        $locale = isset($options['locale']) ? $options['locale'] : $this->getLocale();
        $domain = isset($options['domain']) ? $options['domain'] : self::DEFAULT_TEXT_DOMAIN;
        $catalogue = $this->getCatalogue($locale);
        
        if (!$catalogue)
        {
            $translated = null;
        }
        else
        {
            $translated = $catalogue->getMessage($singular, $domain);
        }
        
        if (!$translated)
        {
            $event = new I18NEvent(
                I18NEvent::MISSING_PLURAL_TRANSLATION, 
                [
                    'message' => [$singular, $plural], 
                    'count' => $count,
                    'domain' => $domain,
                    'locale' => $locale]
            );
            
            $this->dispatch($event);
            $translated = $event->getMessage() ?: [$singular, $plural];
        }
        
        // Pluralize
        $rules = PluralForms::get($locale);

        if (!$rules)
        {
            if ($catalogue && $catalogue->hasMetadata('plural-form'))
            {
                $rules = $catalogue->getMessage('plural-form');

                if (!is_callable($rules))
                {
                    $rules = PluralForms::parse($rules);

                    if ($rules)
                    {
                        $rules = $rules['plural'];
                    }
                }
            }
        }
        else
        {
            $rules = $rules['plural'];
        }

        $id = $rules ? PluralForms::evaluate($count, $rules) : 0;
        $translated = isset($translated[$id]) ? $translated[$id] : ($id > 0 ? $plural : $singular);

        if (isset($options['%']))
        {
            $translated = str_replace(array_keys($options['%']), array_values($options['%']), $translated);
        }
        
        return $translated;
    }
}
