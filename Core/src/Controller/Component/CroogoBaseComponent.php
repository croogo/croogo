<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Controller\Exception\MissingComponentException;
use Cake\Core\Configure;
use Cake\Event\Event;
use Croogo\Core\Croogo;

class CroogoBaseComponent extends ControllerPreparingComponent
{

	public function prepareController(Controller $controller)
	{
		if (!$controller->components()->has('Theme')) {
			$this->loadComponent('Croogo/Core.Theme');
		}
		if (!$controller->components()->has('ViewFallback')) {
			$this->loadComponent('Croogo/Core.ViewFallback');
		}
		if (!$controller->components()->has('Hooks')) {
			$this->loadComponent('Croogo/Core.Hooks');
		}
	}

	public function beforeFilter(Event $event)
	{
		/** @var Controller $controller */
		$controller = $event->subject();

		$aclFilterComponent = 'Filter';
		if (empty($controller->{$aclFilterComponent})) {
			throw new MissingComponentException(array('class' => $aclFilterComponent));
		}
		$controller->{$aclFilterComponent}->auth();

		if (!$controller->request->is('api')) {
			$controller->Security->blackHoleCallback = 'securityError';
			if ($controller->request->param('action') == 'delete' && $controller->request->param('prefix') == 'admin') {
				$controller->request->allowMethod('post');
			}
		}

		if ($controller->request->is('ajax')) {
			$controller->layout = 'ajax';
		}

		if (
			$controller->request->param('prefix') !== 'admin' &&
			Configure::read('Site.status') == 0 &&
			$controller->Auth->user('role_id') != 1
		) {
			if (!$controller->request->is('whitelisted')) {
				$controller->layout = 'Croogo/Core.maintenance';
				$controller->response->statusCode(503);
				$controller->set('title_for_layout', __d('croogo', 'Site down for maintenance'));
				$controller->viewPath = 'Maintenance';
				$controller->render('Croogo/Core.blank');
			}
		}

		if (isset($controller->request->params['locale'])) {
			Configure::write('Config.language', $controller->request->params['locale']);
		}

		if (isset($controller->request->params['admin'])) {
			Croogo::dispatchEvent('Croogo.beforeSetupAdminData', $this);
		}
	}

}
