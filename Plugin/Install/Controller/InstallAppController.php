<?php
class InstallAppController extends AppController {

	public function beforeFilter() {
		$this->Components->unload('Croogo');
		$this->Components->unload('Auth');
	}

}