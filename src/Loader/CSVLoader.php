<?php

namespace Elixir\I18N\Loader;

use Elixir\Config\Loader\LoaderInterface;
use function Elixir\STDLib\csv_to_array;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class CSVLoader implements LoaderInterface
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
    public function load($config)
    {
        $result = csv_to_array($config, false, $this->delimiter, $this->enclosure);
        $messages = [];

        foreach ($result as $row) {
            $id = array_shift($row);

            if ($id) {
                $messages[$id] = $row;
            }
        }

        return [
            'messages' => $messages,
            'metadata' => [],
        ];
    }
}
