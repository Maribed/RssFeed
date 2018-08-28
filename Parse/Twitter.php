<?php

namespace RssFeed\Parse;


use RssFeed\CombineRss;
use RssFeed\Exception\ExceptionNotAvailable;
use RssFeed\RssData;

class Twitter extends ParserBase
{

    const LIMIT_TWITTER_POSTS = 10;

    public function parser($username)
    {
        $consumerKey = CombineRss::TWITTER_CONSUMER_KEY;
        $consumerSecret = CombineRss::TWITTER_CONSUMER_SECRET;
        $accessToken = CombineRss::TWITTER_ACCESS_TOKEN;
        $accessTokenSecret = CombineRss::TWITTER_ACCESS_TOKEN_SECRET;

        $maximum = self::LIMIT_TWITTER_POSTS;

        $oauth_timestamp = time();

        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $base = 'GET&' . rawurlencode($url) . '&' . rawurlencode("count={$maximum}&oauth_consumer_key={$consumerKey}&oauth_nonce={$oauth_timestamp}&oauth_signature_method=HMAC-SHA1&oauth_timestamp={$oauth_timestamp}&oauth_token={$accessToken}&oauth_version=1.0&screen_name={$username}");
        $key = rawurlencode($consumerSecret) . '&' . rawurlencode($accessTokenSecret);
        $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base, $key, true)));
        $oauth_header = "oauth_consumer_key=\"{$consumerKey}\", oauth_nonce=\"{$oauth_timestamp}\", oauth_signature=\"{$signature}\", oauth_signature_method=\"HMAC-SHA1\", oauth_timestamp=\"{$oauth_timestamp}\", oauth_token=\"{$accessToken}\", oauth_version=\"1.0\", ";

        $curl_request = curl_init();
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, array("Authorization: Oauth {$oauth_header}", 'Expect:'));
        curl_setopt($curl_request, CURLOPT_HEADER, false);
        curl_setopt($curl_request, CURLOPT_URL, $url . "?screen_name={$username}&count={$maximum}");
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl_request);
        curl_close($curl_request);

        $decode_json = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ExceptionNotAvailable("Error get twitter posts in decode.");
        }

        if ($decode_json->errors) {
            throw new ExceptionNotAvailable("Error get twitter posts: " . (string)$decode_json->errors[0]->message);
        } else {
            return self::createRssData($decode_json);
        }
    }

    private static function createRssData($items)
    {
        $result_data = [];
        foreach ($items as $key => $value) {
            $rss_data = new RssData();
            $rss_data->setLink("https://twitter.com/" . CombineRss::TWITTER_USER . "/status/" . $value->id_str);
            $rss_data->setDescription($value->text);
            $rss_data->setPubDate(strtotime($value->created_at));
            $result_data[$key] = $rss_data;
        }

        return $result_data;
    }
}
