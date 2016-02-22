<?php

namespace Elixir\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class LoadParser 
{
    /**
     * @param array $data
     * @return array
     */
    public static function parse(array $data) 
    {
        $parsed = [
            'metadata' => [],
            'messages' => []
        ];
        
        if (isset($data['metadata']) && is_array($data['metadata']) && array_values($data['metadata']) !== $data['metadata'])
        {
            $parsed['metadata'] = $data['metadata'];
            unset($data['metadata']);
        }
        
        if (isset($data['messages']) && is_array($data['messages']) && array_values($data['messages']) !== $data['messages'])
        {
            $parsed['messages'] = $data['messages'];
        }
        else
        {
            $parsed['messages'] = $data;
        }
        
        return $parsed;
    }
}
