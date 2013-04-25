<?php

App::uses('ModelBehavior', 'Model');

/**
 * Encoder Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class EncoderBehavior extends ModelBehavior {

/**
 * setup
 *
 * @param Model $model
 * @param array $config
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (is_string($config)) {
			$config = array($config);
		}

		$this->settings[$model->alias] = $config;
	}

/**
 * Encode data
 *
 * Turn array into a JSON
 *
 * @param object $model model
 * @param array $data data
 * @param array $options (optional)
 * @return string
 */
	public function encodeData(Model $model, $data, $options = array()) {
		$_options = array(
			'json' => false,
			'trim' => true,
		);
		$options = array_merge($_options, $options);

		if (is_array($data) && count($data) > 0) {
			// trim
			if ($options['trim']) {
				$elements = array();
				foreach ($data as $id => $d) {
					$d = trim($d);
					if ($d != '') {
						$elements[$id] = '"' . $d . '"';
					}
				}
			} else {
				$elements = $data;
			}

			// encode
			if (count($elements) > 0) {
				if ($options['json']) {
					$output = json_encode($elements);
				} else {
					$output = '[' . implode(',', $elements) . ']';
				}
			} else {
				$output = '';
			}
		} else {
			$output = '';
		}

		return $output;
	}

/**
 * Decode data
 *
 * @param Model $model
 * @param string $data
 * @return array
 */
	public function decodeData(Model $model, $data) {
		if ($data == '') {
			$output = '';
		} else {
			$output = json_decode($data, true);
		}

		return $output;
	}
}
