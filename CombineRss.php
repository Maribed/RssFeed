<?php

namespace RssFeed;

use RssFeed\Parse\Vkontakte;
use RssFeed\Parse\Facebook;
use RssFeed\Parse\Twitter;
use RssFeed\Parse\RssFeed;

class CombineRss
{

    const FACEBOOK_APP_ID = '';
    const FACEBOOK_APP_SECRET = '';
    const TWITTER_USER = '';
    const TWITTER_CONSUMER_KEY = '';
    const TWITTER_CONSUMER_SECRET = '';
    const TWITTER_ACCESS_TOKEN = '';
    const TWITTER_ACCESS_TOKEN_SECRET = '';

    /**
     * @param array $sources
     *
     *      'rss_urls' => [
     *      ],
     *      'social' => [
     *          'tw' => '',
     *          'fb' => '',
     *          'vk' => ''
     *      ]
     *
     * @return RssData[]
     */
    public function get($sources)
    {
        $Twitter = new Twitter();
        $Facebook = new Facebook();
        $Vkontakte = new Vkontakte();
        $RssFeed = new RssFeed();

        $combine = $this->sortItemsByTimestamp(array_merge(
            $RssFeed->getItems($sources['rss_urls']),
            $Twitter->getItems($sources['social']['tw']),
            $Facebook->getItems($sources['social']['fb']),
            $Vkontakte->getItems($sources['social']['vk'])
        ));

        return $combine;
    }

    /**
     * @param RssData[] $items
     * @return array
     */
    private function sortItemsByTimestamp($items)
    {
        if ($items) {
            foreach ($items as $key => $value) {
                $items_count[$key] = (string)strtotime($value->getPubDate());
            }
            array_multisort($items_count, SORT_DESC, SORT_NUMERIC, $items);
        }

        return $items;
    }
}