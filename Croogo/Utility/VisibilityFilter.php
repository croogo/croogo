<?php

namespace Croogo\Croogo\Utility;
App::uses('StringConverter', 'Croogo.Lib/Utility');

/**
 * VisibilityFilter
 *
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @package  Croogo.Croogo.Lib.Utility
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class VisibilityFilter {

/**
 * StringConverter instance
 */
	protected $_converter = null;

/**
 * Known url keys
 */
	protected $_urlKeys = array(
		'admin' => false,
		'plugin' => false,
		'controller' => false,
		'action' => false,
		'named' => false,
		'pass' => false,
	);

/**
 * Constructor
 *
 * @param CakeRequest $request
 */
	public function __construct(CakeRequest $request = null) {
		if ($request) {
			$this->_request = $request;
		} else {
			$this->_request = new CakeRequest();
		}
		$this->_converter = new StringConverter();
	}

/**
 * Check that request (passed in the constructor) is visible based on list of
 * specified rules.  The rules can specified in link string format or just a
 * plain URL fragment.  Whenever possible, use link string formatted rule since
 * a URL fragment can be expensive.
 *
 * The current request is checked against negative rules first (implicitly
 * hidden), then against positive rules (implicitly visible).
 * If there's no positive rule, defaults to visible.
 *
 * @param array $rules Array of rules in link string format
 * @return bool True if the rules are satisfied
 * @see StringConverter::linkStringToArray()
 */
	protected function _isVisible($rules) {
		$negativeRules = array_filter($rules, function($value) {
			if ($value[0] === '-') {
				return true;
			}
			return false;
		});
		foreach ($negativeRules as $rule) {
			if ($this->_ruleMatch(substr($rule, 1))) {
				return false;
			}
		}

		$positiveRules = array_diff($rules, $negativeRules);
		if (empty($positiveRules)) {
			return true;
		}
		foreach ($positiveRules as $rule) {
			if ($rule[0] == '+') {
				$rule = substr($rule, 1);
			}
			if ($this->_ruleMatch($rule)) {
				return true;
			}
		}

		return false;
	}

/**
 * Check that request matches a single rule
 *
 * @param string $rule Rule in link string or plain URL fragment
 * @return bool True if request satisfies the rule
 */
	protected function _ruleMatch($rule) {
		if (strpos($rule, ':') !== false) {
			$url = array_filter($this->_converter->linkStringToArray($rule));
			if (isset($url['?'])) {
				$queryString = $url['?'];
				unset($url['?']);
			}
		} else {
			$url = Router::parse($rule);
			$named = array_diff_key($url, $this->_urlKeys);
			$url['named'] = $named;
		}

		$intersect = array_intersect_key($this->_request->params, $url);
		$matched = $intersect == $url;

		if ($matched && isset($queryString)) {
			$matched = $this->_request->query == $queryString;
		}

		return $matched;
	}

/**
 * Remove values based on rules in visibility_path field.
 *
 * Options:
 *   - model Model alias in $values
 *   - field Field name containing the visibility path rules
 *
 * @param array $values Array of data to filter
 */
	public function remove($values, $options = array()) {
		$options = Hash::merge(array(
			'model' => null,
			'field' => null,
		), $options);
		$model = $options['model'];
		$field = $options['field'];
		$results = array();

		foreach ($values as $value) {
			if (empty($value[$model][$field])) {
				$results[] = $value;
				continue;
			}
			if (!is_array($value[$model][$field])) {
				CakeLog::error('Invalid visibility_path rule');
			}

			if ($this->_isVisible($value[$model][$field])) {
				$results[] = $value;
			}
		}
		return $results;
	}

}
