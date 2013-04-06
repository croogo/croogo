<?php
class SettingData {

	public $table = 'settings';

	public $records = array(
		array(
			'id' => '6',
			'key' => 'Site.title',
			'value' => 'Croogo',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '1',
			'params' => ''
		),
		array(
			'id' => '7',
			'key' => 'Site.tagline',
			'value' => 'A CakePHP powered Content Management System.',
			'title' => '',
			'description' => '',
			'input_type' => 'textarea',
			'editable' => '1',
			'weight' => '2',
			'params' => ''
		),
		array(
			'id' => '8',
			'key' => 'Site.email',
			'value' => 'you@your-site.com',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '3',
			'params' => ''
		),
		array(
			'id' => '9',
			'key' => 'Site.status',
			'value' => '1',
			'title' => '',
			'description' => '',
			'input_type' => 'checkbox',
			'editable' => '1',
			'weight' => '6',
			'params' => ''
		),
		array(
			'id' => '12',
			'key' => 'Meta.robots',
			'value' => 'index, follow',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '6',
			'params' => ''
		),
		array(
			'id' => '13',
			'key' => 'Meta.keywords',
			'value' => 'croogo, Croogo',
			'title' => '',
			'description' => '',
			'input_type' => 'textarea',
			'editable' => '1',
			'weight' => '7',
			'params' => ''
		),
		array(
			'id' => '14',
			'key' => 'Meta.description',
			'value' => 'Croogo - A CakePHP powered Content Management System',
			'title' => '',
			'description' => '',
			'input_type' => 'textarea',
			'editable' => '1',
			'weight' => '8',
			'params' => ''
		),
		array(
			'id' => '15',
			'key' => 'Meta.generator',
			'value' => 'Croogo - Content Management System',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '0',
			'weight' => '9',
			'params' => ''
		),
		array(
			'id' => '16',
			'key' => 'Service.akismet_key',
			'value' => 'your-key',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '11',
			'params' => ''
		),
		array(
			'id' => '17',
			'key' => 'Service.recaptcha_public_key',
			'value' => 'your-public-key',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '12',
			'params' => ''
		),
		array(
			'id' => '18',
			'key' => 'Service.recaptcha_private_key',
			'value' => 'your-private-key',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '13',
			'params' => ''
		),
		array(
			'id' => '19',
			'key' => 'Service.akismet_url',
			'value' => 'http://your-blog.com',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '10',
			'params' => ''
		),
		array(
			'id' => '20',
			'key' => 'Site.theme',
			'value' => '',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '0',
			'weight' => '14',
			'params' => ''
		),
		array(
			'id' => '21',
			'key' => 'Site.feed_url',
			'value' => '',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '0',
			'weight' => '15',
			'params' => ''
		),
		array(
			'id' => '22',
			'key' => 'Reading.nodes_per_page',
			'value' => '5',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '16',
			'params' => ''
		),
		array(
			'id' => '23',
			'key' => 'Writing.wysiwyg',
			'value' => '1',
			'title' => 'Enable WYSIWYG editor',
			'description' => '',
			'input_type' => 'checkbox',
			'editable' => '1',
			'weight' => '17',
			'params' => ''
		),
		array(
			'id' => '24',
			'key' => 'Comment.level',
			'value' => '1',
			'title' => '',
			'description' => 'levels deep (threaded comments)',
			'input_type' => '',
			'editable' => '1',
			'weight' => '18',
			'params' => ''
		),
		array(
			'id' => '25',
			'key' => 'Comment.feed_limit',
			'value' => '10',
			'title' => '',
			'description' => 'number of comments to show in feed',
			'input_type' => '',
			'editable' => '1',
			'weight' => '19',
			'params' => ''
		),
		array(
			'id' => '26',
			'key' => 'Site.locale',
			'value' => 'eng',
			'title' => '',
			'description' => '',
			'input_type' => 'text',
			'editable' => '0',
			'weight' => '20',
			'params' => ''
		),
		array(
			'id' => '27',
			'key' => 'Reading.date_time_format',
			'value' => 'D, M d Y H:i:s',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '21',
			'params' => ''
		),
		array(
			'id' => '28',
			'key' => 'Comment.date_time_format',
			'value' => 'M d, Y',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '1',
			'weight' => '22',
			'params' => ''
		),
		array(
			'id' => '29',
			'key' => 'Site.timezone',
			'value' => '0',
			'title' => '',
			'description' => 'zero (0) for GMT',
			'input_type' => '',
			'editable' => '1',
			'weight' => '4',
			'params' => ''
		),
		array(
			'id' => '32',
			'key' => 'Hook.bootstraps',
			'value' => 'Settings,Comments,Contacts,Nodes,Meta,Menus,Users,Blocks,Taxonomy,FileManager,Wysiwyg,Ckeditor',
			'title' => '',
			'description' => '',
			'input_type' => '',
			'editable' => '0',
			'weight' => '23',
			'params' => ''
		),
		array(
			'id' => '33',
			'key' => 'Comment.email_notification',
			'value' => '1',
			'title' => 'Enable email notification',
			'description' => '',
			'input_type' => 'checkbox',
			'editable' => '1',
			'weight' => '24',
			'params' => ''
		),
		array(
			'id' => '34',
			'key' => 'Access Control.multiRole',
			'value' => '0',
			'title' => 'Enable Multiple Roles',
			'description' => '',
			'input_type' => 'checkbox',
			'editable' => '1',
			'weight' => '25',
			'params' => ''
		),
		array(
			'id' => '35',
			'key' => 'Access Control.rowLevel',
			'value' => '0',
			'title' => 'Row Level Access Control',
			'description' => '',
			'input_type' => 'checkbox',
			'editable' => '1',
			'weight' => '26',
			'params' => ''
		),
		array(
			'id' => '36',
			'key' => 'Access Control.autoLoginDuration',
			'value' => '+1 week',
			'title' => '"Remember Me" Duration',
			'description' => 'Eg: +1 day, +1 week. Leave empty to disable.',
			'input_type' => 'text',
			'editable' => '1',
			'weight' => '27',
			'params' => ''
		),
		array(
			'id' => '37',
			'key' => 'Access Control.models',
			'value' => '',
			'title' => 'Models with Row Level Acl',
			'description' => 'Select models to activate Row Level Access Control on',
			'input_type' => 'multiple',
			'editable' => '1',
			'weight' => '26',
			'params' => 'multiple=checkbox
options={"Nodes.Node": "Node", "Blocks.Block": "Block", "Menus.Menu": "Menu", "Menus.Link": "Link"}'
		),

	);

}
