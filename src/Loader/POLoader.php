<?php

namespace Elixir\I18N\Loader;

use Elixir\Config\Loader\LoaderInterface;
use Sepia\FileHandler;
use Sepia\PoParser;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class POLoader implements LoaderInterface
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
        if (null !== $POParser) {
            $this->setPOParser($POParser);
        } else {
            if (class_exists('\Sepia\PoParser')) {
                $this->setPOParser(function ($file) {
                    $parser = new PoParser(new FileHandler($file));
                    $parser->parse();

                    $messages = [];
                    $metadata = [];
                    $c = 0;

                    foreach ($parser->getEntries() as $entry) {
                        $msgid = current($entry['msgid']);
                        $msgstr = [];

                        if (isset($entry['msgid_plural'])) {
                            $i = 0;

                            while (isset($entry['msgstr['.$i.']'])) {
                                $msgstr[] = current($entry['msgstr['.$i.']']);
                                ++$i;
                            }

                            $metadata['options']['msgid_plural'][$msgid] = current($entry['msgid_plural']);
                        } else {
                            $msgstr[] = current($entry['msgstr']);
                        }

                        if (isset($entry['reference'])) {
                            $metadata['options']['reference'][$msgid] = $entry['reference'];
                        }

                        $messages[$msgid] = $msgstr;
                    }

                    foreach ($parser->getHeaders() as $rawHeader) {
                        list($header, $content) = array_map('trim', explode(':', trim($rawHeader, '"'), 2));
                        $metadata[$header] = substr($content, -2) === '\n' ? substr($content, 0, -2) : $content;
                    }

                    return [
                        'messages' => $messages,
                        'metadata' => $metadata,
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
