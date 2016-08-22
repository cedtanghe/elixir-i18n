<?php

namespace Elixir\I18N;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class LoadParser
{
    /**
     * @param array $data
     *
     * @return array
     */
    public static function parse(array $data)
    {
        $parsed = [
            'metadata' => [],
            'messages' => [],
        ];

        if (isset($data['metadata']) && isset($data['messages'])) {
            $parsed['metadata'] = $data['metadata'];
            $parsed['messages'] = $data['messages'];
        } else {
            $parsed['messages'] = $data;
        }

        return $parsed;
    }
}
