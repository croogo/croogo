<?php

App::uses('CakeRoute', 'Routing/Route');

class ApiRoute extends CakeRoute {

	public function __construct($template, $defaults = array(), $options = array()) {
		$options = Hash::merge(array(
			'api' => Configure::read('Croogo.Api.path'),
			'prefix' => 'v[0-9.]+',
		), $options);
		parent::__construct($template, $defaults, $options);
	}

	public function parse($url) {
		$parsed = parent::parse($url);
		if (!isset($parsed['api']) || !isset($parsed['prefix'])) {
			return false;
		}
		$parsed['prefix'] = str_replace('.', '_', $parsed['prefix']);
		return $parsed;
	}

	public function match($url) {
		if (isset($url['prefix']) && isset($url['action'])) {
			$prefix = $url['prefix'];
			$url['prefix'] = str_replace('_', '.', $url['prefix']);
			$url['action'] = str_replace($prefix . '_', '', $url['action']);
		}
		$match = parent::match($url);
		if ($match && isset($url['action']) && $url['action'] == 'index') {
			$match = str_replace('/index', '', $match);
		}
		return $match;
	}

}
