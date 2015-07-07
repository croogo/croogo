<?php

namespace Croogo\Settings\View\Helper;

use Cake\View\Helper;
use Cake\Utility\Hash;
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

	public $helpers = [
		'Croogo/Core.CroogoForm',
		'Croogo/Core.Croogo',
	];

/**
 * _inputCheckbox
 *
 * @see SettingsFormHelper::input()
 */
	protected function _inputCheckbox($setting, $label) {
		$tooltip = array(
			'data-trigger' => 'hover',
			'data-placement' => 'right',
			'data-title' => $setting->description,
		);
		if ($setting['Setting']['value'] == 1) {
			$output = $this->CroogoForm->input($setting->id, array(
				'type' => $setting->input_type,
				'checked' => 'checked',
				'tooltip' => $tooltip,
				'label' => $label
			));
		} else {
			$output = $this->CroogoForm->input($setting->id, array(
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
	public function input(Setting $setting, $label) {
		$output = '';
		$inputType = ($setting->input_type != null) ? $setting->input_type : 'text';
		if ($setting->input_type == 'multiple') {
			$multiple = true;
			if (isset($setting->params['multiple'])) {
				$multiple = $setting->params['multiple'];
			};
			$selected = json_decode($setting->value);
			$options = json_decode($setting->params['options'], true);
			$output = $this->CroogoForm->input($setting->id, array(
				'label' => $setting->title,
				'multiple' => $multiple,
				'options' => $options,
				'selected' => $selected,
			));
		} elseif ($setting->input_type == 'checkbox') {
			$output = $this->_inputCheckbox($setting, $label);
		} elseif ($setting->input_type == 'radio') {
			$options = json_decode($setting->params['options'], true);
			$output = $this->CroogoForm->input($setting->id, array(
				'label' => $setting->title,
				'type' => 'radio',
				'options' => $options,
				'value' => $setting->value,
			));
		} else {
			$options = [
				'type' => $inputType,
				'id' => 'setting-' . $setting->id,
				'class' => 'span10',
				'value' => $setting->value,
				'help' => $setting->description,
				'label' => $label,
			];

			if ($inputType === 'link') {
				$options = Hash::merge($options, [
					'type' => 'text',
					'linkChooser' => true
				]);
			}

			$output = $this->CroogoForm->input($setting->id, $options);
		}
		return $output;
	}

}
