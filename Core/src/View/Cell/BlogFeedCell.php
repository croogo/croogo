<?php

namespace Croogo\Core\View\Cell;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use Cake\Utility\Xml;
use Cake\View\Cell;
use Croogo\Core\Link;

class BlogFeedCell extends Cell
{

    public function dashboard()
    {
        $posts = $this->getPosts();

        $this->set('posts', $posts);
    }

    protected function getPosts()
    {
        $posts = Cache::read('croogo_blog_feed_posts');
        if ($posts === false) {
            $xml = Xml::build(file_get_contents('https://blog.croogo.org/promoted.rss'));

            $data = Xml::toArray($xml);

            $posts = [];
            foreach ($data['rss']['channel']['item'] as $item) {
                $posts[] = (object)[
                    'title' => $item['title'],
                    'url' => new Link($item['link']),
                    'body' => $item['description'],
                    'date' => new Time($item['pubDate']),
                ];
            }
        }

        Cache::write('croogo_blog_feed_posts', $posts);

        return $posts;
    }
}
