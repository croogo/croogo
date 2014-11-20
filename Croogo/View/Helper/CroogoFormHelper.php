<?php

App::uses('FormHelper', 'View/Helper');
App::uses('CroogoTheme', 'Extensions.Lib');

/**
 * Croogo Form Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class CroogoFormHelper extends FormHelper {

	public $helpers = array(
		'Theme' => array('className' => 'Croogo.Theme'),
		'Html' => array('className' => 'Croogo.CroogoHtml')
	);

/**
 * Constructor
 */
	public function __construct(View $View, $settings = array()) {
		$themeConfig = CroogoTheme::config($View->theme);
		$themeSettings = $themeConfig['settings'];
		$settings = Hash::merge($themeSettings, $settings);
		parent::__construct($View, $settings);
	}

/**
 * Generate bootstrap specific options
 *
 * @return array Options array
 */
	protected function _bootstrapGenerate($title, $options) {
		if (isset($options['button'])) {
			$class = isset($options['class']) ? $options['class'] : null;
			if ($options['button'] === 'default') {
				$options['class'] = 'btn btn-default';
			} else {
				$options['class'] = 'btn btn-' . $options['button'];
			}
			$options['class'] .= $class ? ' ' . $class : null;
			unset($options['button']);
		}
		if (isset($options['icon'])) {
			$title = $this->Html->icon($options['icon']) . ' ' . $title;
			unset($options['icon']);
		}
		return array($title, $options);
	}

	protected function _helpText($options) {
		$helpClass = isset($options['helpInline']) ? 'help-inline' : 'help-block';
		$helpText = $this->Html->tag('span', $options['help'], array(
			'class' => $helpClass,
		));
		$options['after'] = isset($options['after']) ? $options['after'] . $helpText : $helpText;
		unset($options['help'], $options['helpInline']);
		return $options;
	}

	protected function _tooltip($options) {
		if ($options['tooltip'] === false) {
			unset($options['title']);
			return $options;
		}
		$tooltipOptions = array(
			'data-placement' => 'right',
			'data-trigger' => 'focus',
		);
		if (is_string($options['tooltip'])) {
			$options['tooltip'] = array('data-title' => $options['tooltip']);
		}
		if (isset($options['title']) && empty($options['tooltip']['data-title'])) {
			$tooltipOptions['data-title'] = $options['title'];
		}

		$tooltipOptions = Hash::merge($tooltipOptions, $options['tooltip']);
		unset($options['title']);
		unset($options['tooltip']);

		if (isset($options['type']) && in_array($options['type'], array('checkbox', 'radio', 'select'))) {
			if (isset($options['div'])) {
				if (is_string($options['div'])) {
					$options['div'] = array('div' => $options['div']) + $tooltipOptions;
				} else {
					$options['div'] += $tooltipOptions;
				}
			} else {
				$options['div'] = $tooltipOptions;
			}
		} else {
			$options += $tooltipOptions;
		}
		return $options;
	}

/**
 * placeholderOptions
 */
	protected function _placeholderOptions($fieldName, $options = array()) {
		$autoPlaceholder = empty($options['placeholder']) &&
			isset($this->_inputDefaults['placeholder']) &&
			$this->_inputDefaults['placeholder'] === true;
		$autoPlaceholder = $autoPlaceholder ||
			(isset($options['placeholder']) && $options['placeholder'] === true);
		if ($autoPlaceholder) {
			if (!empty($options['title'])) {
				$options['placeholder'] = $options['title'];
			} else {
				if (strpos($fieldName, '.') !== false) {
					$fieldNameE = explode('.', $fieldName);
					$placeholder = end($fieldNameE);
					if (substr($placeholder, -3) == '_id') {
						$placeholder = substr($placeholder, 0, -3);
					}
				} else {
					$placeholder = $fieldName;
				}
				$options['placeholder'] = Inflector::humanize($placeholder);
			}
		}
		return $options;
	}

/**
 * Generate div options for input
 *
 * @param array $options Options list
 * @return array
 */
	protected function _divOptions($options) {
		$divOptions = parent::_divOptions($options);
		$divOptions = $this->_divOptionsAddon($options, $divOptions);
		return $divOptions;
	}

/**
 * Generate addon specific options
 *
 * @param array $options Options list
 * @return array
 */
	protected function _divOptionsAddon($options, $divOptions) {
		if (isset($this->_addon) && isset($divOptions['class'])) {
			$divOptions['class'] .= ' ' . $this->_addon;
			unset($this->_addon);
		}
		return $divOptions;
	}

/**
 * Generate input options array
 *
 * @param array $options Options list
 * @return array
 */
	protected function _parseOptions($options) {
		$options = parent::_parseOptions($options);
		$options = $this->_parseOptionsAddon($options);

		$formInput = $this->Theme->getCssClass('formInput');
		$isMultipleCheckbox = isset($options['multiple']) &&
			$options['multiple'] === 'checkbox';
		$isRadioOrCheckbox = isset($options['type']) &&
			in_array($options['type'], array('checkbox', 'radio'));

		if ($isMultipleCheckbox || $isRadioOrCheckbox) {
			if ($options['type'] == 'radio') {
				$class = $this->Theme->getCssClass('radioClass');
			} elseif ($options['type'] == 'checkbox') {
				$class = $this->Theme->getCssClass('checkboxClass');
			}
			if (empty($class) && isset($options['class'])) {
				$class = str_replace($formInput, '', $options['class']);
			}
			if (empty($class)) {
				unset($options['class']);
			} else {
				$options['class'] = $class;
			}
		}

		return $options;
	}

/**
 * Generate addon specific input options array
 *
 * @param array $options Options list
 * @return array
 */
	protected function _parseOptionsAddon($options) {
		if (isset($options['append'])) {
			$this->_addon = 'input-append';
		}
		if (isset($options['prepend'])) {
			$this->_addon = 'input-prepend';
		}

		if (isset($this->_addon)) {
			$options['class'] = $this->Theme->getCssClass('columnLeft');
			if (isset($options['append'])) {
				$options['between'] = '<div class="addon">';
				$options['after'] = $options['addon'] . '</div>';
			}
			if (isset($options['prepend'])) {
				$options['between'] = '<div class="addon">' . $options['addon'];
				$options['after'] = '</div>';
			}
		}

		unset($options['append'], $options['prepend'], $options['addon']);

		return $options;
	}

/**
 * Normalize field name
 *
 * @return array Map of normalized field names and corresponding list of roleIds
 */
	protected function _setupFieldAccess($fieldAccess) {
		$map = array();
		foreach ($fieldAccess as $field => $config) {
			if (strpos($field, '.') === false) {
				$field = $this->defaultModel . '.' . $field;
			}
			$map[$field] = (array)$config;
		}
		return $map;
	}

/**
 * Checks if field is editable by current user's role
 *
 * @return bool True if field is editable
 */
	protected function _isEditable($field) {
		if (strpos($field, '.') === false) {
			$field = $this->defaultModel . '.' . $field;
		}
		if (isset($this->_fieldAccess[$field])) {
			return in_array($this->_currentRoleId, $this->_fieldAccess[$field]);
		}
		return true;
	}

/**
 * Returns an HTML FORM element.
 *
 * @see FormHelper::create()
 * @return string A formatted opening FORM tag
 */
	public function create($model = null, $options = array()) {
		if (!empty($options['fieldAccess'])) {
			$this->_fieldAccess = $this->_setupFieldAccess($options['fieldAccess']);
			$this->_currentRoleId = $this->_View->Layout->getRoleId();
			unset($options['fieldAcess']);
		}
		if (!empty($this->settings['inputDefaults'])) {
			$options = Hash::merge(array(
				'inputDefaults' => $this->settings['inputDefaults'],
			), $options);
		}
		$formInputClass = $this->Theme->getCssClass('formInput');
		if (
			empty($options['inputDefaults']['class']) &&
			!empty($formInputClass)
		) {
			$options['inputDefaults']['class'] = $formInputClass;
		}
		return parent::create($model, $options);
	}

	public function input($fieldName, $options = array()) {
		if (!$this->_isEditable($fieldName)) {
			return null;
		}
		$options = $this->_placeholderOptions($fieldName, $options);

		// Automatic tooltip when label is 'false'. Leftover from 1.5.0.
		//
		// TODO:
		// Remove this behavior in 1.6.x, ie: tooltip needs to be implicitly
		// requested by caller.
		if (empty($options['title']) && empty($options['label']) && !empty($options['placeholder']) && !isset($options['tooltip'])) {
			$options['tooltip'] = $options['placeholder'];
		}

		if (!empty($options['help'])) {
			$options = $this->_helpText($options);
		}

		if (array_key_exists('tooltip', $options)) {
			$options = $this->_tooltip($options);
		}

		return parent::input($fieldName, $options);
	}

/**
 * Try to guess autocomplete default values
 *
 * @param string $field field name
 * @param array $config setting passed to CroogoFormHelper::autocomplete()
 * @return array Array of id and display value
 */
	protected function _acDefaults($field, $config) {
		$displayKey = $displayValue = null;
		if (isset($this->request->data[$this->defaultModel][$field])) {
			$displayKey = $this->request->data[$this->defaultModel][$field];
		}

		if (substr($field, -3) === '_id') {
			$varName = Inflector::variable(Inflector::pluralize(substr($field, 0, -3)));
			if (isset($this->_View->viewVars[$varName])) {
				$lookupData = $this->_View->viewVars[$varName];
				if (isset($lookupData[$displayKey])) {
					$displayValue = $lookupData[$displayKey];
				}
			}
		}

		$defaults = array($displayKey => $displayValue);
		return array_filter($defaults);
	}

/**
 * Generates an autocomplete text input that works with bootstrap's typeahead
 *
 * Besides the standard Form::input() $options, this method accepts:
 *
 *   'autocomplete' array with the following keys to configure fields to use
 *   from the AJAX result:
 *      `data-displayField`: field to display in the autocomplete dropdown
 *      `data-primaryKey`: field to use as the primary identifier
 *      `data-queryField`: field to use as the AJAX querystring
 *      `data-relatedElement`: selector to the input storing the actual value
 *      `data-url`: url to retrieve autocomplete data
 *
 * @see FormHelper::input()
 */
	public function autocomplete($fieldName, $options = array()) {
		$options = Hash::merge(array(
			'type' => 'text',
			'default' => null,
			'value' => null,
			'class' => null,
			'autocomplete' => array(
				'default' => null,
				'data-displayField' => null,
				'data-primaryKey' => null,
				'data-queryField' => null,
				'data-relatedElement' => null,
				'data-url' => null,
			),
		), $options);

		if (strpos($fieldName, '.') !== false) {
			list($model, $field) = explode('.', $fieldName);
			$unlockField = $model . '.' . $field;
		} else {
			$field = $fieldName;
			$unlockField = $this->defaultModel . '.' . $field;
		}
		$defaults = $this->_acDefaults($field, $options['autocomplete']);

		$default = isset($options['default']) ? $options['default'] : key($defaults);
		$hiddenOptions = array_filter(array(
			'type' => 'hidden',
			'default' => $default,
		));
		$out = $this->input($fieldName, $hiddenOptions);

		$this->unlockField($unlockField);

		$autocomplete = $options['autocomplete'];
		$label = isset($options['label']) ? $options['label'] : Inflector::humanize($field);

		$default = isset($autocomplete['default']) ? $autocomplete['default'] : array_shift($defaults);
		$inputDefaults = $this->_View->Form->inputDefaults();
		$class = null;
		if (!empty($inputDefaults['class'])) {
			$class = $inputDefaults['class'];
		}
		$class = $options['class'] ? $options['class'] : $class;
		$autocomplete = Hash::merge($autocomplete, array(
			'type' => $options['type'],
			'label' => $label,
			'class' => trim($class . ' typeahead-autocomplete'),
			'default' => $default,
			'autocomplete' => 'off',
		));
		$out .= $this->input("autocomplete_${field}", $autocomplete);

		return $out;
	}

	public function button($title, $options = array()) {
		$defaults = array('button' => 'default');
		$options = array_merge($defaults, $options);

		list($title, $options) = $this->_bootstrapGenerate($title, $options);

		return parent::button($title, $options);
	}

	public function submit($caption = null, $options = array()) {
		$defaults = array('button' => 'default', 'escape' => false);
		$options = array_merge($defaults, $options);

		list($caption, $options) = $this->_bootstrapGenerate($caption, $options);

		return parent::submit($caption, $options);
	}

}
