<?php

namespace Croogo\Core\View\Helper;

use Cake\View\Helper\UrlHelper as CakeUrlHelper;
use Croogo\Core\Router;

class UrlHelper extends CakeUrlHelper
{
    /**
     * Returns a URL based on provided parameters.
     *
     * @param string|array|\Croogo\Core\Link|null $url Either a relative string url
     *    like `/products/view/23`, an array of URL parameters or a Link object.
     *    Using an array for URLs will allow you to leverage the reverse routing
     *    features of CakePHP.
     * @param bool $full If true, the full base URL will be prepended to the result
     * @return string Full translated URL with base path.
     */
    public function build($url = null, $full = false)
    {
        return h(Router::url($url, $full));
    }
}
