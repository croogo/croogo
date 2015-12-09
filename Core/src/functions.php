<?php

namespace Croogo\Core;

use Croogo\Core\Link;

if (!function_exists('\Croogo\Core\linkFromLinkString')) {
    /**
     * @param string $link
     *
     * @return \Croogo\Core\Link
     */
    function linkFromLinkString($link)
    {
        return Link::createFromLinkString($link);
    }
}

if (!function_exists('\Croogo\Core\link')) {
    /**
     * @param array|string $url
     *
     * @return \Croogo\Core\Link
     */
    function link($url)
    {
        return new Link($url);
    }
}
