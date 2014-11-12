<?php

App::uses('CakeEventListener', 'Event');

/**
 * FileManagerEventHandler
 *
 * @category Event
 * @package  Croogo.FileManager.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManagerEventHandler implements CakeEventListener {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Controller.Links.setupLinkChooser' => array(
				'callable' => 'onSetupLinkChooser',
			),
		);
	}

/**
 * Setup Link chooser values
 *
 * @return void
 */
	public function onSetupLinkChooser($event) {
		$linkChoosers = array();
		$linkChoosers['Images'] = array(
			'title' => 'Images',
			'description' => 'Attachments with an image mime type.',
			'url' => array(
				'plugin' => 'file_manager',
				'controller' => 'attachments',
				'action' => 'index',
				'?' => array(
					'chooser_type' => 'image',
					'chooser' => 1,
					'KeepThis' => true,
					'TB_iframe' => true,
					'height' => 400,
					'width' => 600
				)
			)
		);
		$linkChoosers['Files'] = array(
			'title' => 'Files',
			'description' => 'Attachments with other mime types, ie. pdf, xls, doc, etc.',
			'url' => array(
				'plugin' => 'file_manager',
				'controller' => 'attachments',
				'action' => 'index',
				'?' => array(
					'chooser_type' => 'file',
					'chooser' => 1,
					'KeepThis' => true,
					'TB_iframe' => true,
					'height' => 400,
					'width' => 600
				)
			)
		);
		Croogo::mergeConfig('Menus.linkChoosers', $linkChoosers);
	}

}
