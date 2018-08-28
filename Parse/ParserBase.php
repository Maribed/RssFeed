<?php

namespace RssFeed\Parse;

use RssFeed\Exception\ExceptionNotAvailable;

abstract class ParserBase
{
    /**
     * @param $sources
     * @return array
     */
    public function getItems($sources)
    {
        try {
            if ($sources) {
                return $this->parser($sources);
            }
        } catch (ExceptionNotAvailable $e) {
            $this->sendLogMail('RssFeed. Ошибка при парсинге.', $e->getMessage());
        }

        return [];
    }

    abstract public function parser($sources);
}