<?php

namespace Elixir\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface I18NInterface
{
    /**
     * @var string
     */
    const DEFAULT_TEXT_DOMAIN = 'messages';
    
    /**
     * @param string $value
     */
    public function setLocale($value);
    
    /**
     * @return string
     */
    public function getLocale();
    
    /**
     * @param string $message
     * @param array $options
     * @return string
     */
    public function translate($message, array $options = []);
    
    /**
     * @param string $singular
     * @param string $plural
     * @param int $number
     * @param array $options
     * @return string
     */
    public function transPlural($singular, $plural, $number, array $options = []);
}
