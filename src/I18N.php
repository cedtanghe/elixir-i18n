<?php

namespace Elixir\I18N;

use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Dispatcher\DispatcherTrait;
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
     * {@inheritdoc}
     */
    public function translate($message, array $options = []);
    
    /**
     * {@inheritdoc}
     */
    public function transPlural($singular, $plural, $count, array $options = []);
}
