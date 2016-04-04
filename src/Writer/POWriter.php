<?php

namespace Elixir\I18N\Writer;

use Elixir\Config\Writer\WriterInterface;
use Sepia\PoParser;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class POWriter implements WriterInterface
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
                $this->setPOParser(function($messages, $metadata)
                {
                    $parser = new PoParser();
                    
                    // Todo
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
    public function dump(array $data) 
    {
        $messages = $data;
        $metadata = [];
        
        if (isset($data['messages']) && isset($data['metadata']))
        {
            $messages = $data['messages'];
            $metadata = $data['metadata'];
        }
        
        return call_user_func_array($this->getPOParser(), [$messages, $metadata]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.po'))
        {
            $file .= '.po';
        }
        
        file_put_contents($file, $this->dump($data));
        return file_exists($file);
    }
}
