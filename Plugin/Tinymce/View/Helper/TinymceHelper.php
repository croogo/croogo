<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Tinymce Helper
 *
 * PHP version 5
 *
 * @category Tinymce.Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TinymceHelper extends AppHelper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Js',
	);

/**
 * Actions
 *
 * Format: ControllerName/action_name => settings
 *
 * @var array
 */
	public $actions = array();

/**
 * Default settings for tinymce
 *
 * @var array
 * @access public
 */
	public $settings = array(
		// General options
		'mode' => 'exact',
		'elements' => '',
		'theme' => 'advanced',
		'relative_urls' => false,
		'plugins' => 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
		'width' => '100%',
		'height' => '250px',

		// Theme options
		'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
		'theme_advanced_buttons2' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor',
		'theme_advanced_buttons3' => 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
		//'theme_advanced_buttons4' => 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
		'theme_advanced_toolbar_location' => 'top',
		'theme_advanced_toolbar_align' => 'left',
		'theme_advanced_statusbar_location' => 'bottom',
		'theme_advanced_resizing' => true,

		// Example content CSS (should be your site CSS)
		//'content_css' => 'css/content.css',

		// Drop lists for link/image/media/template dialogs
		'template_external_list_url' => 'lists/template_list.js',
		'external_link_list_url' => 'lists/link_list.js',
		'external_image_list_url' => 'lists/image_list.js',
		'media_external_list_url' => 'lists/media_list.js',

		// Attachments browser
		'file_browser_callback' => 'fileBrowserCallBack',
	);

/**
 * getSettings
 *
 * @param array $settings
 * @return array
 */
	public function getSettings($settings = array()) {
		$_settings = $this->settings;
		$action = Inflector::camelize($this->params['controller']) . '/' . $this->params['action'];
		if (isset($this->actions[$action])) {
			$settings = array();
			foreach ($this->actions[$action] as $action) {
				$settings[] = Hash::merge($_settings, $action);
			}
		}
		return $settings;
	}

/**
 * beforeRender
 *
 * @param string $viewFile
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->Html->script('/tinymce/js/wysiwyg', array('inline' => false));
		if (is_array(Configure::read('Tinymce.actions'))) {
			$this->actions = Hash::merge($this->actions, Configure::read('Tinymce.actions'));
		}
		$action = Inflector::camelize($this->params['controller']) . '/' . $this->params['action'];
		if (Configure::read('Writing.wysiwyg') && isset($this->actions[$action])) {
			$this->Html->script('/tinymce/js/tiny_mce', array('inline' => false));
			$settings = $this->getSettings();
			foreach ($settings as $setting) {
				$this->Html->scriptBlock('tinyMCE.init(' . $this->Js->object($setting) . ');', array('inline' => false));
			}
		}
	}
}
