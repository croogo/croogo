<?php

use Cake\Core\Configure;

function addCroogoPluginPath($basePluginName) {
	$pluginPaths = Configure::read('plugins');
	if (!isset($pluginPaths['Croogo'])) {
		$pluginPaths['Croogo'] = realpath('..') . DS;
	}

	$pluginPaths['Croogo/' . $basePluginName] = $pluginPaths['Croogo'] . $basePluginName . DS;

	Configure::write('plugins', $pluginPaths);
}

addCroogoPluginPath('Core');
addCroogoPluginPath('Acl');
addCroogoPluginPath('Settings');
addCroogoPluginPath('Comments');
addCroogoPluginPath('Contacts');
addCroogoPluginPath('Nodes');
addCroogoPluginPath('Meta');
addCroogoPluginPath('Menus');
addCroogoPluginPath('Users');
addCroogoPluginPath('Blocks');
addCroogoPluginPath('Taxonomy');
addCroogoPluginPath('FileManager');
addCroogoPluginPath('Wysiwyg');
addCroogoPluginPath('Dashboards');
