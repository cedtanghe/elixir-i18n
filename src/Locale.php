<?php

namespace Elixir\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Locale 
{
    /**
     * @var string
     */
    protected static $locale;

    /**
     * @param string $header
     * @return string
     */
    public static function acceptFromHttp($header = null)
    {
        $header = $header ?: isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
        
        if ($header)
        {
            if (class_exists('\Locale')) 
            {
                return \Locale::acceptFromHttp($header);
            } 
            else 
            {
                $code = explode(';', $header);
                $code = explode(',', $code[0]);

                return $code[0];
            }
        }
        
        return null;
    }
    
    /**
     * @param string $locale
     */
    public static function setDefault($locale)
    {
        if (class_exists('\Locale')) 
        {
            \Locale::setDefault($locale);
        }
        else
        {
            static::$locale = $locale;
        }
    }

    /**
     * @return string
     */
    public static function getDefault() 
    {
        if (class_exists('\Locale')) 
        {
            return \Locale::getDefault();
        } 
        else
        {
            if (null === static::$locale) 
            {
                static::$locale = static::acceptFromHttp();
            }
            
            return static::$locale;
        }
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $arguments)
    {
        if (class_exists('\Locale'))
        {
            return call_user_func_array(['\Locale', $method], $arguments);
        }

        throw new \RuntimeException('Class "\Locale" does not exist, please install the "intl" extension.');
    }
}
