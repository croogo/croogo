<?php
$viewPaths = Configure::read('viewPaths');
$viewPaths['0'] = str_ireplace('views', 'plugins'.DS.'install'.DS.'views', $viewPaths['0']);
Configure::write('viewPaths', $viewPaths);

class InstallAppController extends AppController {

    function beforeFilter() {
        
    }

}
?>