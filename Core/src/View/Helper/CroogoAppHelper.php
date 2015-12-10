<?php

namespace Croogo\Core\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Croogo Application helper
 *
 * @category Helpers
 * @package  Croogo.Croogo.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAppHelper extends Helper
{

    public $helpers = [
        'Url'
    ];

/**
 * Url helper function
 *
 * @param string $url
 * @param bool $full
 * @return mixed
 * @access public
 */
    public function url($url = null, $full = false)
    {
        if (isset($this->request->params['locale'])) {
            if ($url === null || (is_array($url) && !isset($url['locale']))) {
                $url['locale'] = $this->request->params['locale'];
            }
        }
        return $this->Url->build($url, $full);
    }

/**
 * Convenience method to generate an API Url
 *
 * @param string|array $url
 * @param bool $full
 * @return string
 */
    public function apiUrl($url = null, $full = false)
    {
        if (is_array($url)) {
            $url = Hash::merge([
                'admin' => false,
                'api' => Configure::read('Croogo.Api.path'),
                'prefix' => 'v1.0',
                'ext' => 'json',
            ], $url);
        }
        return $this->Url->build($url, $full);
    }
}
