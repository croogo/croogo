<?php

namespace Croogo\Core;

use ArrayObject;
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
        $copy = array_map(function ($val) {
            if (is_array($val)) {
                return $val;
            }
            return urldecode($val);
        }, $this->getArrayCopy());
        unset($copy['pass']);
        return (isset($this->controller)) ? $copy : $this->url;
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
