<?php

namespace Croogo\Core;

use ArrayObject;
use Cake\Log\Log;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Croogo\Core\Utility\StringConverter;

class Link extends ArrayObject
{

    public static function createFromLinkString($link)
    {
        $stringConverter = new StringConverter();

        return new Link($stringConverter->linkStringToArray($link));
    }

    public function __construct($url)
    {
        if (is_array($url)) {
            $this->exchangeArray($url);
        } elseif (is_string($url)) {
            $this->url = $url;
        }
    }

    public function getUrl()
    {
        $copy = $this->getArrayCopy();
        unset($copy['pass']);
        foreach ($copy as $key => $val) {
            if (is_array($val)) {
                continue;
            }
            $val = urldecode($val);
            if (boolval($val) === false) {
                $val = false;
            } elseif ($val[0] == '#') {
                unset($copy[$key]);
                $copy['#'] = mb_substr($val, 1);
            }
        }

        return (isset($this->controller)) ? $copy : urldecode($this->url);
    }

    public function getPath()
    {
        try {
            $url = $this->getUrl();
            if (is_string($url)) {
                return urldecode($url);
            }

            return Router::url($url);
        } catch (MissingRouteException $e) {
            Log::error('Croogo/Core.Link::getPath() cannot get url');
            Log::error($e->getMessage());

            return '/';
        }
    }

    public function toLinkString()
    {
        $stringConverter = new StringConverter();

        return $stringConverter->urlToLinkString($this->getArrayCopy());
    }

    public function __toString()
    {
        return (isset($this->controller)) ? $this->toLinkString() : $this->url;
    }

    public function __get($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this[$name]);
    }
}
