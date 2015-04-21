<?php

namespace Croogo\Settings\View\Helper;

use Cake\View\Helper;
use Croogo\Settings\Model\Entity\Setting;

/**
 * SettingForms Helper
 *
 * @category Helper
 * @package  Croogo.Settings.View.Helper
 * @version  1.0
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsFormHelper extends Helper {

	public $helpers = array(
		'Form'
	);

/**
 * _inputCheckbox
 *
 * @see SettingsFormHelper::input()
 */
	protected function _inputCheckbox($setting, $label, $i) {
		$tooltip = array(
			'data-trigger' => 'hover',
			'data-placement' => 'right',
			'data-title' => $setting->description,
		);
		if ($setting['Setting']['value'] == 1) {
			$output = $this->Form->input('Setting.' . $i . '.value', array(
				'type' => $setting->input_type,
				'checked' => 'checked',
				'tooltip' => $tooltip,
				'label' => $label
			));
		} else {
			$output = $this->Form->input('Setting.' . $i . '.value', array(
				'type' => $setting->input_type,
				'tooltip' => $tooltip,
				'label' => $label
			));
		}
		return $output;
	}

/**
 * Renders input setting according to its type
 *
 * @param Setting $setting setting data
 * @param string $label Input label
 * @param integer $i index
 * @return string
 */
	public function input(Setting $setting, $label, $i) {
		$output = '';
		$inputType = ($setting->input_type != null) ? $setting->input_type : 'text';
		if ($setting->input_type == 'multiple') {
			$multiple = true;
			if (isset($setting['Params']['multiple'])) {
				$multiple = $setting['Params']['multiple'];
			};
			$selected = json_decode($setting->value);
			$options = json_decode($setting['Params']['options'], true);
			$output = $this->Form->input('Setting.' . $i . '.values', array(
				'label' => $setting->title,
				'multiple' => $multiple,
				'options' => $options,
				'selected' => $selected,
			));
		} elseif ($setting->input_type == 'checkbox') {
			$output = $this->_inputCheckbox($setting, $label, $i);
		} elseif ($setting->input_type == 'radio') {
			$value = $setting->value;
			$options = json_decode($setting['Params']['options'], true);
			$output = $this->Form->input('Setting.' . $i . '.value', array(
				'legend' => $setting->title,
				'type' => 'radio',
				'options' => $options,
				'value' => $value,
			));
		} else {
			$output = $this->Form->input('Setting.' . $i . '.value', array(
				'type' => $inputType,
				'class' => 'span10',
				'value' => $setting->value,
				'help' => $setting->description,
				'label' => $label,
			));
		}
		return $output;
	}

}
