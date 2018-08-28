<?php
namespace RssFeed\Parse;


use RssFeed\Exception\ExceptionNotAvailable;
use RssFeed\RssData;

class Vkontakte extends ParserBase
{

    const LIMIT_VK_POSTS = 10;

    public function parser($id_feed)
    {
        $maximum = self::LIMIT_VK_POSTS;

        $response = file_get_contents("http://api.vk.com/method/wall.get?owner_id=-{$id_feed}&v=5.21&filter=owner&count={$maximum}");

        $decode_json = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ExceptionNotAvailable("Error get vkontakte posts in decode.");
        }

        if ($decode_json->error) {
            throw new ExceptionNotAvailable("Error get vkontakte posts: " . (string)json_decode($response)->error->error_msg);
        }

        return self::createRssData($decode_json->response->items);
    }

    private static function createRssData($items)
    {
        $result_data = [];
        foreach ($items as $key => $value) {
            if ($value->text != '') {
                $rss_data = new RssData();
                $rss_data->setLink("https://vk.com/wall" . $value->owner_id . "?own=1&w=wall" . $value->owner_id . "_" . $value->id);
                $rss_data->setDescription($value->text);
                $rss_data->setPubDate($value->date);
                $result_data[$key] = $rss_data;
            }
        }

        return $result_data;
    }
}
