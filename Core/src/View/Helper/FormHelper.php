<?php

namespace Croogo\Core\View\Helper;

use BootstrapUI\View\Helper\FormHelper as BaseFormHelper;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\View;
use Croogo\Extensions\CroogoTheme;

/**
 * Croogo Form Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class FormHelper extends BaseFormHelper
{

    public $helpers = [
        'Html',
        'Url',
        'Croogo/Core.Theme',
        'Croogo/Core.Croogo',
        'Croogo/Core.Html',
    ];

    /**
     * Constructor
     */
    public function __construct(View $View, $settings = [])
    {
        $settings = Hash::merge([
            'widgets' => [
                'stringlist' => [
                    'Croogo/Core.StringList',
                    'textarea',
                    'label',
                ],
                'datetime' => ['Croogo/Core.DateTime', 'select'],
                'button' => ['Croogo/Core.Button']
            ],
        ], $settings);

        if ($View->getTheme()) {
            $themeConfig = CroogoTheme::config($View->getTheme());
            $themeSettings = $themeConfig['settings'];
            $settings = Hash::merge($themeSettings, $settings);
        }

        parent::__construct($View, $settings);
    }

    protected function _tooltip($options)
    {
        if ($options['tooltip'] === false) {
            unset($options['title']);

            return $options;
        }
        $tooltipOptions = [
            'data-placement' => 'right',
            'data-trigger' => 'focus',
        ];
        if (is_string($options['tooltip'])) {
            $options['tooltip'] = ['data-title' => $options['tooltip']];
        }
        if (isset($options['title']) && empty($options['tooltip']['data-title'])) {
            $tooltipOptions['data-title'] = $options['title'];
        }

        $tooltipOptions = Hash::merge($tooltipOptions, $options['tooltip']);
        unset($options['title']);
        unset($options['tooltip']);

        if (isset($options['type']) && in_array($options['type'], ['checkbox', 'radio', 'select'])) {
            if (isset($options['div'])) {
                if (is_string($options['div'])) {
                    $options['div'] = ['div' => $options['div']] + $tooltipOptions;
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
    protected function _placeholderOptions($fieldName, $options = [])
    {
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
     * Generate input options array
     *
     * @param array $fieldName Options list
     * @return array
     */
    protected function _parseOptions($fieldName, $options)
    {
        $options = parent::_parseOptions($fieldName, $options);

        $formInput = $this->Theme->getCssClass('formInput');
        $isMultipleCheckbox = isset($options['multiple']) &&
            $options['multiple'] === 'checkbox';
        $isRadioOrCheckbox = isset($options['type']) &&
            in_array($options['type'], ['checkbox', 'radio']);

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

        if ((array_key_exists('linkChooser', $options)) && ($options['linkChooser'])) {
            $target = '#' . $options['id'];
            $options['append'] = $this->Croogo->linkChooser($target);
        }

        return $options;
    }

    /**
     * Normalize field name
     *
     * @return array Map of normalized field names and corresponding list of roleIds
     */
    protected function _setupFieldAccess($fieldAccess)
    {
        $map = [];
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
    protected function _isEditable($field)
    {
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
     * @return string A formatted opening FORM tag
     * @see FormHelper::create()
     */
    public function create($model = null, array $options = [])
    {
        if (!empty($options['fieldAccess'])) {
            $this->_fieldAccess = $this->_setupFieldAccess($options['fieldAccess']);
            $this->_currentRoleId = $this->_View->Layout->getRoleId();
            unset($options['fieldAccess']);
        }

        return parent::create($model, $options);
    }

    public function input($fieldName, array $options = [])
    {
        return $this->control($fieldName, $options);
    }

    public function control($fieldName, array $options = [])
    {
        if (!$this->_isEditable($fieldName)) {
            return null;
        }
        $options = $this->_placeholderOptions($fieldName, $options);

        if (array_key_exists('tooltip', $options)) {
            $options = $this->_tooltip($options);
        }

        return parent::control($fieldName, $options);
    }

    /**
     * Try to guess autocomplete default values
     *
     * @param string $field field name
     * @param array $config setting passed to CroogoFormHelper::autocomplete()
     * @return array Array of id and display value
     */
    protected function _acDefaults($field, $config)
    {
        $displayKey = $displayValue = null;
        $request = $this->getView()->getRequest();
        list(, $table) = pluginSplit($this->context()->entity()->getSource());
        if (isset($request->getData($table)[$field])) {
            $displayKey = $request->getData($table)[$field];
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

        $defaults = [$displayKey => $displayValue];

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
    public function autocomplete($fieldName, $options = [])
    {
        $options = Hash::merge([
            'type' => 'text',
            'default' => null,
            'value' => null,
            'class' => null,
            'autocomplete' => [
                'default' => null,
                'data-displayField' => null,
                'data-primaryKey' => null,
                'data-queryField' => null,
                'data-relatedElement' => null,
                'data-url' => null,
            ],
        ], $options);

        $field = $fieldName;
        $defaults = $this->_acDefaults($field, $options['autocomplete']);

        $default = isset($options['default']) ? $options['default'] : key($defaults);
        $hiddenOptions = array_filter([
            'type' => 'hidden',
            'default' => $default,
        ]);
        $out = $this->input($fieldName, $hiddenOptions);

        $this->unlockField($fieldName);

        $autocomplete = $options['autocomplete'];
        $label = isset($options['label']) ? $options['label'] : Inflector::humanize($field);

        $default = isset($autocomplete['default']) ? $autocomplete['default'] : array_shift($defaults);
        $inputDefaults = $this->_View->Form->getTemplates();
        $class = null;
        if (!empty($inputDefaults['class'])) {
            $class = $inputDefaults['class'];
        }
        $class = $options['class'] ? $options['class'] : $class;
        $autocomplete = Hash::merge($autocomplete, [
            'type' => $options['type'],
            'label' => $label,
            'class' => trim($class . ' typeahead-autocomplete'),
            'default' => $default,
            'autocomplete' => 'off',
        ]);
        $out .= $this->input("autocomplete_${field}", $autocomplete);

        return $out;
    }
}
