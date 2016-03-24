<?php

namespace Elixir\I18N\Loader;

use Elixir\Config\Loader\LoaderInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class MO implements LoaderInterface
{
    /**
     * @var resource
     */
    protected $file;
    
    /**
     * @var boolean
     */
    protected $littleEndian;
    
    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function load($config)
    {
        $this->file = fopen($config, 'rb');
        
        $magic = fread($this->file, 4);
        
        if ($magic === "\x95\x04\x12\xde")
        {
            $this->littleEndian = false;
        } 
        else if($magic === "\xde\x12\x04\x95") 
        {
            $this->littleEndian = true;
        } 
        else
        {
            fclose($this->file);
            throw new \InvalidArgumentException('This is not a valid mo file.');
        }
        
        // Major revision
        $this->readInteger() >> 16;
        
        $numStrings = $this->readInteger();
        $originalStringTableOffset = $this->readInteger();
        $translationStringTableOffset = $this->readInteger();
        
        fseek($this->file, $originalStringTableOffset);
        $originalStringTable = $this->readIntegerList(2 * $numStrings);
        
        fseek($this->file, $translationStringTableOffset);
        $translationStringTable = $this->readIntegerList(2 * $numStrings);
        
        $data = [];
        
        for ($i = 0; $i < $numStrings; ++$i) 
        {
            $sizeKey = $i * 2 + 1;
            $offsetKey = $i * 2 + 2;
            $originalStringSize = $originalStringTable[$sizeKey];
            $originalStringOffset = $originalStringTable[$offsetKey];
            $translationStringSize = $translationStringTable[$sizeKey];
            $translationStringOffset = $translationStringTable[$offsetKey];
            $originalString = [''];
            
            if ($originalStringSize > 0) 
            {
                fseek($this->file, $originalStringOffset);
                $originalString = explode("\0", fread($this->file, $originalStringSize));
            }

            if ($translationStringSize > 0) 
            {
                fseek($this->file, $translationStringOffset);
                $translationString = explode("\0", fread($this->file, $translationStringSize));

                if (count($originalString) > 1 && count($translationString) > 1) 
                {
                    $data[$originalString[0]] = $translationString;
                    array_shift($originalString);

                    foreach ($originalString as $string) 
                    {
                        $data[$string] = '';
                    }
                } 
                else 
                {
                    $data[$originalString[0]] = $translationString[0];
                }
            }
        }
        
        $metadata = [];
        
        if (array_key_exists('', $data)) 
        {
            $rawHeaders = explode("\n", trim($data['']));
            
            foreach ($rawHeaders as $rawHeader) 
            {
                list($header, $content) = array_map('trim', explode(':', $rawHeader, 2));
                $metadata[$header] = $content;
            }
            
            unset($data['']);
        }
        
        $messages = $data;
        fclose($this->file);
        
        return [
            'messages' => $messages,
            'metadata' => $metadata
        ];
    }
    
    /**
     * @return integer
     */
    protected function readInteger()
    {
        $format = $this->littleEndian ? 'Vint' : 'Nint';
        $result = unpack($format, fread($this->file, 4));

        return $result['int'];
    }
    
    /**
     * @param integer $num
     * @return integer
     */
    protected function readIntegerList($num)
    {
        $format = $this->littleEndian ? 'V' . $num : 'N' . $num;
        return unpack($format, fread($this->file, 4 * $num));
    }
}
