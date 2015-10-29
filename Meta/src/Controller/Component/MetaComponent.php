<?php

namespace Croogo\Meta\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Croogo\Core\Croogo;

/**
 * Meta Component
 *
 * @package Croogo.Meta.Controller.Component
 */
class MetaComponent extends Component {

/**
 * startup
 */
	public function startup(Event $event) {
		$this->_adminTabs($event->subject());
	}

/**
 * Hook admin tabs for controllers whom its primary model has MetaBehavior attached.
 */
	protected function _adminTabs(Controller $controller) {
		list(, $modelName) = pluginSplit($controller->modelClass, true);
		$table = $controller->{$modelName};
		if ((!$table) || (!$table->hasBehavior('Meta'))) {
			return;
		}

		$title = __d('croogo', 'Meta');
		$element = 'Croogo/Meta.admin/meta_tab';
		Croogo::hookAdminTab('Admin/' . $controller->name . '/add', $title, $element);
		Croogo::hookAdminTab('Admin/' . $controller->name . '/edit', $title, $element);
	}
}

