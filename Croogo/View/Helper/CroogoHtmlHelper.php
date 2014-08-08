<?php

App::uses('HtmlHelper', 'View/Helper');

/**
 * Croogo Html Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class CroogoHtmlHelper extends HtmlHelper {

	public function __construct(View $View, $settings = array()) {
		$settings = Hash::merge(array(
			'iconDefaults' => array(
				'classDefault' => '',
				'largeIconClass' => 'icon-large',
				'smallIconClass' => '',
				'classPrefix' => 'icon-',
			),
		), $settings);
		parent::__construct($View, $settings);

		$boxIconClass = trim(
			$settings['iconDefaults']['classDefault'] . ' ' .
			$settings['iconDefaults']['classPrefix'] . 'list'
		);

		$this->_tags['beginbox'] =
			'<div class="row-fluid">
				<div class="span12">
					<div class="box">
						<div class="box-title">
							<i class="' . $boxIconClass. '"></i>
							%s
						</div>
						<div class="box-content %s">';
		$this->_tags['endbox'] =
						'</div>
					</div>
				</div>
			</div>';
		$this->_tags['icon'] = '<i class="%s"%s></i> ';
	}

	public function beginBox($title, $isHidden = false, $isLabelHidden = false) {
		$isHidden = $isHidden ? 'hidden' : '';
		$isLabelHidden = $isLabelHidden ? 'label-hidden' : '';
		$class = $isHidden . ' ' . $isLabelHidden;
		return $this->useTag('beginbox', $title, $class);
	}

	public function endBox() {
		return $this->useTag('endbox');
	}

	public function icon($name, $options = array()) {
		$iconDefaults = $this->settings['iconDefaults'];
		$defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$class = $iconDefaults['classDefault'];
		foreach ((array)$name as $iconName) {
			$class .= ' ' . $iconDefaults['classPrefix'] . $iconName;
		}
		$class .= ' ' . $options['class'];
		$class = trim($class);
		unset($options['class']);
		$attributes = '';
		foreach ($options as $attr => $value) {
			$attributes .= $attr . '="' . $value . '" ';
		}
		if ($attributes) {
			$attributes = ' ' . $attributes;
		}
		return sprintf($this->_tags['icon'], $class, $attributes);
	}

	public function status($value, $url = array()) {
		$icon = $value == CroogoStatus::PUBLISHED ? 'ok' : 'remove';
		$class = $value == CroogoStatus::PUBLISHED ? 'green' : 'red';
		$iconDefaults = $this->settings['iconDefaults'];

		if (empty($url)) {
			return $this->icon($icon, array('class' => $class));
		} else {
			return $this->link('', 'javascript:void(0);', array(
				'data-url' => $this->url($url),
				'class' => $iconDefaults['classPrefix'] . $icon . ' ' . $class . ' ajax-toggle',
			));
		}
	}

/**
 * Add possibilities to parent::link() method
 *
 * ### Options
 *
 * - `escape` Set to true to enable escaping of title and attributes.
 * - `button` 'primary', 'info', 'success', 'warning', 'danger', 'inverse', 'link'. http://twitter.github.com/bootstrap/base-css.html#buttons
 * - `icon` 'ok', 'remove' ... http://fortawesome.github.com/Font-Awesome/
 *
 * @param string $title The content to be wrapped by <a> tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array $options Array of HTML attributes.
 * @param string $confirmMessage JavaScript confirmation message.
 * @return string An `<a />` element.
 */
	public function link($title, $url = null, $options = array(), $confirmMessage = false) {
		$defaults = array('escape' => false);
		$options = is_null($options) ? array() : $options;
		$options = array_merge($defaults, $options);
		$iconDefaults = $this->settings['iconDefaults'];

		if (!empty($options['button'])) {
			$buttons = array('btn');
			foreach ((array)$options['button'] as $button) {
				if ($button == 'default') {
					continue;
				}
				$buttons[] = 'btn-' . $button;
			}
			$options['class'] = trim(join(' ', $buttons));
			unset($options['button']);
		}

		if (isset($options['icon'])) {
			$iconSize = $iconDefaults['largeIconClass'];
			if (isset($options['iconSize']) && $options['iconSize'] === 'small') {
				$iconSize = $iconDefaults['smallIconClass'];
				unset($options['iconSize']);
			}
			if (empty($options['iconInline'])) {
				$title = $this->icon($options['icon'], array('class' => $iconSize)) . $title;
			} else {
				$icon = trim($iconSize . ' ' . $iconDefaults['classPrefix'] . $options['icon']);
				if (isset($options['class'])) {
					$options['class'] .= ' ' . $icon;
				} else {
					$options['class'] = ' ' . $icon;
				}
				unset($options['iconInline']);
			}
			unset($options['icon']);
		}

		if (isset($options['tooltip'])) {
			$tooltipOptions = array(
				'rel' => 'tooltip',
				'data-placement' => 'top',
				'data-trigger' => 'hover',
			);
			if (is_string($options['tooltip'])) {
				$tooltipOptions = array_merge(array(
					'data-title' => $options['tooltip'],
				), $tooltipOptions);
				$options = array_merge($options, $tooltipOptions);
			} else {
				$options['tooltip'] = array_merge($tooltipOptions, $options['tooltip']);
				$options = array_merge($options, $options['tooltip']);
			}
			unset($options['tooltip']);
		}

		return parent::link($title, $url, $options, $confirmMessage);
	}

	public function addPath($path, $separator) {
		$path = explode($separator, $path);
		$currentPath = '';
		foreach ($path as $p) {
			if (!is_null($p)) {
				$currentPath .= $p . $separator;
				$this->addCrumb($p, $currentPath);
			}
		}
		return $this;
	}

	public function addCrumb($name, $link = null, $options = null) {
		parent::addCrumb($name, $link, $options);
		return $this;
	}

	public function hasCrumbs() {
		return !empty($this->_crumbs);
	}

}
