<?php

App::uses('Helper', 'View');

/**
 * Croogo Application helper
 *
 * This file is the base helper of all other helpers
 *
 * PHP version 5
 *
 * @category Helpers
 * @package  Croogo.Croogo.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAppHelper extends Helper {

/**
 * Url helper function
 *
 * @param string $url
 * @param bool $full
 * @return mixed
 * @access public
 */
	public function url($url = null, $full = false) {
		if (!isset($url['locale']) && isset($this->params['locale'])) {
			$url['locale'] = $this->params['locale'];
		}
		return parent::url($url, $full);
	}

}
