<?php
class ExtensionsAppController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Security->requirePost('admin_delete', 'admin_toggle', 'admin_activate');
	}

}
