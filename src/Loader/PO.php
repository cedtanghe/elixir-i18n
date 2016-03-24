<?php

namespace Elixir\I18N\Loader;

use Elixir\Config\Loader\LoaderInterface;
use Sepia\FileHandler;
use Sepia\PoParser;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PO implements LoaderInterface
{
    /**
     * @var callable 
     */
    protected $POParser;
    
    /**
     * @param callable $POParser
     */
    public function __construct(callable $POParser = null)
    {
        if (null !== $POParser)
        {
            $this->setPOParser($POParser);
        } 
        else 
        {
            if (class_exists('\Sepia\PoParser'))
            {
                $this->setPOParser(function($file)
                {
                    $parser = new PoParser(new FileHandler($file));
                    $parser->parse();
                    
                    $messages = [];
                    $metadata = [];
                    
                    foreach ($parser->getEntries() as $entry) 
                    {
                        $msgstr = [];
                        $i = 0;
                        
                        while (isset($entry['msgstr[' . $i . ']']))
                        {
                            $msgstr[] = current($entry['msgstr[' . $i . ']']);
                            $i++;
                        }
                        
                        $messages[current($entry['msgid'])] = $msgstr;
                    }
                    
                    foreach ($parser->getHeaders() as $rawHeader) 
                    {
                        list($header, $content) = explode(':', $rawHeader, 2);
                        $metadata[$header] = substr($content, -3) === '\n"' ? substr($content, 0, -3) : $content;
                    }
                    
                    return [
                        'messages' => $messages,
                        'metadata' => $metadata
                    ];
                });
            }
        }
    }
    
    /**
     * @param callable $value
     */
    public function setPOParser(callable $value)
    {
        $this->POParser = $value;
    }
    
    /**
     * @return callable
     */
    public function getPOParser()
    {
        return $this->POParser;
    }
    
    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        return call_user_func($this->getPOParser(), $config);
    }
}
