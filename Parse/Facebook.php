<?php
namespace RssFeed\Parse;

use RssFeed\CombineRss;
use RssFeed\Exception\ExceptionNotAvailable;
use RssFeed\RssData;

class Facebook extends ParserBase
{

    const LIMIT_FACEBOOK_POSTS = 10;

    public function parser($id_feed)
    {

        $appID = CombineRss::FACEBOOK_APP_ID;
        $appSecret = CombineRss::FACEBOOK_APP_SECRET;

        $maximum = self::LIMIT_FACEBOOK_POSTS;

        $authentication = file_get_contents("https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id={$appID}&client_secret={$appSecret}");

        $response = file_get_contents("https://graph.facebook.com/{$id_feed}/feed?{$authentication}&limit={$maximum}");

        if (!$response) {
            throw new ExceptionNotAvailable("Error get Facebook posts.");
        }

        $decode_json = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ExceptionNotAvailable("Error get Facebook posts in decode.");
        }

        return self::createRssData($decode_json->data);
    }

    private static function createRssData($items)
    {
        $result_data = [];
        foreach ($items as $key => $value) {
            $rss_data = new RssData();
            $rss_data->setLink('https://www.facebook.com/' . $value->id);
            $rss_data->setDescription($value->message);
            $rss_data->setPubDate(strtotime($value->created_time));
            $result_data[$key] = $rss_data;
        }

        return $result_data;
    }
}
