<?php

namespace Elixir\I18N\Writer;

use Elixir\Config\Writer\WriterInterface;
use Elixir\I18N\PluralForms;
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
                $this->setPOParser(function($locale, $domain, $messages, $metadata)
                {
                    $parser = new PoParser();
                    
                    // Headers
                    $headers = [];
                    $metadata['Language'] = $locale;
                    
                    if (!isset($metadata['X-Generator']))
                    {
                        $metadata['X-Generator'] = 'Elixir';
                    }
                    
                    if (!isset($metadata['PO-Revision-Date']))
                    {
                        $metadata['PO-Revision-Date'] = date('Y-m-d H:iO');
                    }
                    
                    if (!isset($metadata['MIME-Version']))
                    {
                        $metadata['MIME-Version'] = '1.0';
                    }
                    
                    if (!isset($metadata['Content-Type']))
                    {
                        $metadata['Content-Type'] = 'text/plain; charset=utf8';
                    }
                    
                    if (!isset($metadata['Content-Transfert-Encoding']))
                    {
                        $metadata['Content-Transfert-Encoding'] = '8bit';
                    }
                    
                    if (!isset($metadata['Plural-Forms']) || is_callable($metadata['Plural-Forms']))
                    {
                        $pluralForms = PluralForms::get($locale);
                        
                        if ($pluralForms)
                        {
                            $metadata['Plural-Forms'] = $pluralForms['rule'];
                        }
                    }
                    
                    foreach ($metadata as $key => $value)
                    {
                        if ($key === 'options')
                        {
                            continue;
                        }
                        
                        $headers[] = sprintf('"%s: %s\n"', $key, $value);
                    }
                    
                    $parser->setHeaders($headers);
                    
                    // Entries
                    foreach ($messages as $id => $translation)
                    {
                        $entry = ['msgid' => $id];
                        $translation = (array)$translation;
                        
                        if (count($translation) <= 1)
                        {
                            $entry['msgstr'] = $translation;
                        }
                        else
                        {
                            if (isset($metadata['options'][$domain]['msgid_plural'][$id]))
                            {
                                $msgidPlural = $metadata['options'][$domain]['msgid_plural'][$id];
                            }
                            else
                            {
                                $msgidPlural = $id;
                            }
                            
                            $entry['msgid_plural'] = (array)$msgidPlural;
                            $i = 0;
                            
                            foreach ($translation as $t)
                            {
                                $entry['msgstr[' . $i++ . ']'] = (array)$t;
                            }
                        }
                        
                        if (isset($metadata['options'][$domain]['reference'][$id]))
                        {
                            $entry['reference'] = (array)$metadata['options'][$domain]['reference'][$id];
                        }
                        
                        $parser->setEntry($id, $entry);
                    }
                    
                    return $parser->compile();
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
        return call_user_func_array(
            $this->getPOParser(), 
            [
                $data['locale'], 
                $data['domain'], 
                $data['messages'], 
                $data['metadata']
            ]
        );
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
