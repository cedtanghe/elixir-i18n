<?php

namespace Elixir\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */

interface I18NInterface
{
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
     * @param string $message
     * @param int $count
     * @param array $options
     * @return string
     */
    public function transPlural($message, $count, array $options = []);
}
