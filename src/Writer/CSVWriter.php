<?php

namespace Elixir\I18N\Writer;

use Elixir\Config\Writer\WriterInterface;
use const CSV_FORCE_UTF8;
use function Elixir\STDLib\array_to_csv;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class CSVWritter implements WriterInterface
{
    /**
     * @var string
     */
    protected $delimiter;
    
    /**
     * @var string
     */
    protected $enclosure;
    
    /**
     * @param string $delimiter
     * @param string $enclosure
     */
    public function __construct($delimiter = ';', $enclosure = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }
    
    /**
     * {@inheritdoc}
     */
    public function dump(array $data) 
    {
        $messages = isset($data['messages']) ? $data['messages'] : $data;
        return array_to_csv($messages, false, $this->delimiter, $this->enclosure);
    }
    
    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.csv'))
        {
            $file .= '.csv';
        }
        
        file_put_contents($file, CSV_FORCE_UTF8 . $this->dump($data));
        return file_exists($file);
    }
}
