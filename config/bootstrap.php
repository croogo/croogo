<?php

use Cake\Core\Configure;

function addCroogoPluginPath($basePluginName) {
	$pluginPaths = Configure::read('plugins');
	$pluginPaths['Croogo/' . $basePluginName] = $pluginPaths['Croogo'] . $basePluginName . DS;

	Configure::write('plugins', $pluginPaths);
}

addCroogoPluginPath('Croogo');
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
addCroogoPluginPath('Ckeditor');
