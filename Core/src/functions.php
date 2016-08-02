<?php

namespace Croogo\Core;

use Croogo\Core\Link;
use DebugKit\DebugTimer;

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

if (!function_exists('\Croogo\Core\timerStart')) {
    function timerStart($name, $message = null)
    {
        if (!Plugin::available('DebugKit')) {
            return;
        }

        DebugTimer::start($name, $message);
    }
}

if (!function_exists('\Croogo\Core\timerStop')) {
    function timerStop($name)
    {
        if (!Plugin::available('DebugKit')) {
            return;
        }

        DebugTimer::stop($name);
    }
}

if (!function_exists('\Croogo\Core\time')) {
    function time(callable $callable, $name, $message = null)
    {
        timerStart($name, $message);

        call_user_func($callable);

        timerStop($name);
    }
}
