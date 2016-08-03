<?php

namespace Croogo\Taxonomy\Routing\Route;

use Cake\ORM\TableRegistry;
use Cake\Routing\Route\Route;

/**
 * Class TypeRoute
 */
class TypeRoute extends Route
{
    /**
     * @param string $type The type to check
     * @return bool
     */
    protected function _checkType($type)
    {
        $typeCount = TableRegistry::get('Croogo/Taxonomy.Types')
            ->findByAlias($type)
            ->cache(sprintf('%s_count', $type), 'croogo_types')
            ->count();

        return $typeCount !== 0;
    }

    /**
     * Checks to see if the given URL can be parsed by this route.
     *
     * If the route can be parsed an array of parameters will be returned; if not
     * false will be returned. String URLs are parsed if they match a routes regular expression.
     *
     * @param string $url The URL to attempt to parse.
     * @param string $method The HTTP method of the request being parsed.
     * @return array|false An array of request parameters, or false on failure.
     */
    public function parse($url, $method = '')
    {
        $url = parent::parse($url, $method);
        if ($this->_checkType($url['type'])) {
            return $url;
        }

        return false;
    }

    /**
     * Check if a URL array matches this route instance.
     *
     * If the URL matches the route parameters and settings, then
     * return a generated string URL. If the URL doesn't match the route parameters, false will be returned.
     * This method handles the reverse routing or conversion of URL arrays into string URLs.
     *
     * @param array $url An array of parameters to check matching with.
     * @param array $context An array of the current request context.
     *   Contains information such as the current host, scheme, port, base
     *   directory and other url params.
     * @return string|false Either a string URL for the parameters if they match or false.
     */
    public function match(array $url, array $context = [])
    {
        if (empty($url['type'])) {
            return false;
        }

        //This is a special case for generating example permalinks
        if ($url['type'] === '_placeholder') {
            return parent::match($url, $context);
        }

        if ($this->_checkType($url['type'])) {
            return parent::match($url, $context);
        }
        return false;
    }

}
