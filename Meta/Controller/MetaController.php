<?php

namespace Croogo\Meta\Controller;

use Meta\Controller\MetaAppController;
/**
 * Meta Controller
 *
 * @category Meta.Controller
 * @package  Croogo.Meta
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaController extends MetaAppController {

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);

/**
 * Preset Variable Search
 *
 * @var array
 * @access public
 */
	public $presetVars = true;

/**
 * Admin delete meta
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete_meta($id = null) {
		$Meta = ClassRegistry::init('Meta.Meta');
		$success = false;
		if ($id != null && $Meta->delete($id)) {
			$success = true;
		} else {
			if (!$Meta->exists($id)) {
				$success = true;
			}
		}

		$success = array('success' => $success);
		$this->set(compact('success'));
		$this->set('_serialize', 'success');
	}

/**
 * Admin add meta
 *
 * @return void
 * @access public
 */
	public function admin_add_meta() {
		$this->layout = 'ajax';
	}

}
