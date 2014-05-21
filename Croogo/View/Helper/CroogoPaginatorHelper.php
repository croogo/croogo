<?php

namespace Croogo\Croogo\View\Helper;

use App\View\Helper\PaginatorHelper;
/**
 * Croogo Paginator Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class CroogoPaginatorHelper extends PaginatorHelper {

/**
 * doesn't use parent::numbers()
 *
 * @param type $options
 * @return boolean
 */
	public function numbers($options = array()) {
		$defaults = array(
			'tag' => 'li',
			'model' => $this->defaultModel(),
			'modulus' => '8',
			'class' => null
		);
		$options = array_merge($defaults, $options);
		extract($options);

		$params = $this->params($options['model']);
		extract($params);

		$begin = $page - floor($modulus / 2);
		$end = $begin + $modulus;
		if ($end > $pageCount) {
			$end = $pageCount + 1;
			$begin = $pageCount - $modulus;
		}
		$begin = $begin <= 0 ? 1 : $begin;

		$output = '';
		for ($i = $begin; $i < $end; $i++) {
			$class = ($i == $page) ? 'active' : '';
			$output .= $this->Html->tag($tag, $this->link($i, array('page' => $i), compact('class')));
		}
		return $output;
	}

	protected function _defaultOptions($options) {
		if (!isset($options['tag'])) {
			$options['tag'] = 'li';
		}

		return $options;
	}

	public function prev($title = '<< Previous', $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		$options['escape'] = isset($options['escape']) ? $options['escape'] : false;
		$options = $this->_defaultOptions($options, false);
		return parent::prev($title, $options, $this->link($title), $disabledOptions);
	}

	public function next($title = 'Next >>', $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		$options['escape'] = isset($options['escape']) ? $options['escape'] : false;
		$options = $this->_defaultOptions($options, false);
		return parent::next($title, $options, $this->link($title), $disabledOptions);
	}

	public function first($first = '<< first', $options = array()) {
		$options['escape'] = isset($options['escape']) ? $options['escape'] : true;
		$options = $this->_defaultOptions($options);
		return parent::first($first, $options);
	}

	public function last($last = 'last >>', $options = array()) {
		$options['escape'] = isset($options['escape']) ? $options['escape'] : true;
		$options = $this->_defaultOptions($options);
		return parent::last($last, $options);
	}

}
