<?php
/**
 * CroogoRouter
 *
 * NOTE: Do not use this class as a substitute of Routes class.
 * Use it only in /app/config/routes.php
 *
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoRouter {
/**
 * An extra Route will be created for locale-based URLs
 *
 * For example,
 * http://yoursite.com/blog/post-title, and
 * http://yoursite.com/eng/blog/post-title
 *
 * Returns this object's routes array. Returns false if there are no routes available.
 *
 * @param string $route			An empty string, or a route string "/"
 * @param array $default		NULL or an array describing the default route
 * @param array $params			An array matching the named elements in the route to regular expressions which that element should match.
 * @return void
 */
    function connect($route, $default = array(), $params = array()) {
        Router::connect($route, $default, $params);
        Router::connect('/:locale' . $route, $default, array_merge(array('locale' => '[a-z]{3}'), $params));
    }

}
?>