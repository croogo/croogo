<?php

App::uses('HtmlHelper', 'View/Helper');

/**
 * Croogo Html Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class CroogoHtmlHelper extends HtmlHelper {

/**
 * Constructor
 */
	public function __construct(View $View, $settings = array()) {
		$settings = Hash::merge(array(
			'iconDefaults' => array(
				'classDefault' => '',
				'largeIconClass' => 'icon-large',
				'smallIconClass' => '',
				'classPrefix' => 'icon-',
			),
			'icons' => array(
				'check-mark' => 'ok',
				'x-mark' => 'remove',
			),
		), $settings);
		parent::__construct($View, $settings);

		$themeCss = $this->_View->Layout->cssClass();
		$boxIconClass = trim(
			$settings['iconDefaults']['classDefault'] . ' ' .
			$settings['iconDefaults']['classPrefix'] . 'list'
		);

		$this->_tags['beginbox'] =
			"<div class='$themeCss[row]'>
				<div class='$themeCss[columnFull]'>
					<div class='box'>
						<div class='box-title'>
							<i class='$boxIconClass'></i>
							%s
						</div>
						<div class='box-content %s'>";
		$this->_tags['endbox'] =
						'</div>
					</div>
				</div>
			</div>';
		$this->_tags['icon'] = '<i class="%s"%s></i> ';
	}

/**
 * Create a string representing the start of a box container
 *
 * @param string $title Box title
 * @param boolean $isHidden When true, container will have 'hidden' class
 * @param boolean $isLabelHidden When true, container will have 'label-hidden' class
 * @returns string Start of box markup
 */
	public function beginBox($title, $isHidden = false, $isLabelHidden = false) {
		$isHidden = $isHidden ? 'hidden' : '';
		$isLabelHidden = $isLabelHidden ? 'label-hidden' : '';
		$class = $isHidden . ' ' . $isLabelHidden;
		return $this->useTag('beginbox', $title, $class);
	}

/**
 * Create a string that ends a box container
 *
 * @return string Box end markup
 */
	public function endBox() {
		return $this->useTag('endbox');
	}

/**
 * Returns a icon markup
 *
 * @param string $name Icon name/identifier without the prefix
 * @param array $options Icon html attributes
 * @return string Icon markup
 */
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

/**
 * Create a link with icons with XHR toggleable status values
 *
 * @param string $value Current value
 * @param array $url Url in array format
 */
	public function status($value, $url = array()) {
		$iconDefaults = $this->settings['iconDefaults'];
		$icons = $this->settings['icons'];
		$icon = $value == CroogoStatus::PUBLISHED ? $icons['check-mark'] : $icons['x-mark'];
		$class = $value == CroogoStatus::PUBLISHED ? 'green' : 'red';

		if (empty($url)) {
			return $this->icon($icon, array('class' => $class));
		} else {
			return $this->link('', 'javascript:void(0);', array(
				'data-url' => $this->url($url),
				'class' => trim(implode(' ', array(
					$iconDefaults['classDefault'],
					$iconDefaults['classPrefix'] . $icon,
					$class,
					'ajax-toggle',
				)))
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
			if (!empty($options['class'])) {
				$class = $options['class'];
			}
			$buttons = array('btn');
			foreach ((array)$options['button'] as $button) {
				$buttons[] = 'btn-' . $button;
			}
			$options['class'] = trim(join(' ', $buttons));
			$options['class'] .= isset($class) ? ' ' . $class : null;
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

/**
 * @deprecated Use FileManagerHelper::breadcrumb()
 */
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

/**
 * Checks that crumbs has been added/initialied
 *
 * @return boolean True if crumbs has been populated
 */
	public function hasCrumbs() {
		return !empty($this->_crumbs);
	}

/**
 * Starts a new tab pane
 *
 * @param string $id Tab pane id
 * @param array $options Options array
 * @return string
 */
	public function tabStart($id, $options = array()) {
		$options = Hash::merge(array(
			'id' => $id,
			'class' => 'tab-pane',
		), $options);
		return $this->useTag('blockstart', $options);
	}

/**
 * Ends a tab pane
 *
 * @return string
 */
	public function tabEnd() {
		return $this->useTag('blockend');
	}

}
