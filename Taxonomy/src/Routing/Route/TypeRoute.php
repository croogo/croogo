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

        $typeCount = TableRegistry::get('Croogo/Taxonomy.Types')
            ->findByAlias($url['type'])
            ->cache(sprintf('%s_count', $url['type']), 'croogo_types')
            ->count();

        if ($typeCount === 0) {
            return false;
        }

        $matchedUrl = parent::match($url, $context);
        return $matchedUrl;
    }

}
