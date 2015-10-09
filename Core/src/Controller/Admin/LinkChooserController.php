<?php

namespace Croogo\Core\Controller\Admin;

use Cake\Event\Event;
use Croogo\Core\Controller\CroogoAppController;
use Croogo\Core\Croogo;
use Cake\Core\Configure;

class LinkChooserController extends CroogoAppController
{

	public function linkChooser() {
		Croogo::dispatchEvent('Controller.Links.setupLinkChooser', $this);
		$linkChoosers = Configure::read('Croogo.linkChoosers');
		$this->set(compact('linkChoosers'));
	}

}
