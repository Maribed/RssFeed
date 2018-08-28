<?php

namespace RssFeed\Parse;

use RssFeed\Exception\ExceptionNotAvailable;
use RssFeed\RssData;

/**
 * Парсер rss
 */
class RssFeed extends ParserBase
{
    /**
     * Получение всех итемов всех rss
     *
     * @param  array $urls урлы rss
     * @return array
     * @throws ExceptionNotAvailable
     */
    public function parser($urls)
    {
        $items = [];

        foreach ($urls as $url) {
            $headers = @get_headers($url);
            if (preg_match("/(200 OK)$/", $headers[0])){
                $xml = simplexml_load_file($url);
                $items = array_merge($items, self::getItemsFromXml($xml->channel));
            } else {
                throw new ExceptionNotAvailable("Error get rss. Headers: " . $headers[0]);
            }
        }

        return $items;
    }

    /**
     * Получение итемов rss
     *
     * @param \SimpleXMLElement $xml
     * @return string|array
     */
    private static function getItemsFromXml(\SimpleXMLElement $xml = null)
    {
        if (!$xml->children()) {
            return (string)$xml;
        }

        $items = [];

        foreach ($xml->children()->item as $tag => $child) {
            $items[] = self::createRssData($child);
        }

        return $items;
    }

    private static function createRssData($item){
        $rss_data = new RssData();
        $rss_data->setTitle($item->title);
        $rss_data->setLink(preg_replace('/\?.*/', '', $item->link));
        $rss_data->setDescription($item->description);
        $rss_data->setAuthor($item->author);
        $rss_data->setPubDate(strtotime($item->pubDate));

        return $rss_data;
    }
}
