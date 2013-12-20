<?php
App::uses('CroogoRouter', 'Croogo.Lib');
App::uses('Helper', 'View');

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
		$arrayUrl = array();
		if (!is_array($url)) {
			$arrayUrl = Router::parse($url, $full);
		} else {
			$arrayUrl = $url;
		}

		if (empty($arrayUrl['locale']) && !empty($this->request->params['locale'])) {
			$arrayUrl['locale'] = $this->request->params['locale'];
		}
		return parent::url($arrayUrl, $full);
	}

}
