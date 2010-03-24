<?php
/**
 * Install Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallController extends InstallAppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Install';
/**
 * No models required
 *
 * @var array
 * @access public
 */
    var $uses = null;
/**
 * No components required
 *
 * @var array
 * @access public
 */
    var $components = null;
/**
 * beforeFilter
 *
 * If the bootstrap file exists - the app is already installed - disactivate
 *
 * @return void
 */
    function beforeFilter() {
        parent::beforeFilter();

        $this->layout = 'install';
        App::import('Component', 'Session');
        $this->Session = new SessionComponent;
		if (file_exists(CONFIGS . 'croogo_bootstrap.php')) {
			$this->Session->setFlash('Already Installed');
			$this->redirect('/');
		}
    }
/**
 * Step 0: welcome
 *
 * A simple welcome message for the installer.
 *
 * @return void
 */
    function index() {
        $this->set('title_for_layout', __('Installation: Welcome', true));
    }
/**
 * Step 1: database
 *
 * @return void
 */
    function database() {
        $this->set('title_for_layout', __('Step 1: Database', true));
        if (!empty($this->data)) {
            // test database connection
            if (mysql_connect($this->data['Install']['host'], $this->data['Install']['login'], $this->data['Install']['password']) &&
                mysql_select_db($this->data['Install']['database'])) {
                // copy database.php.install
                copy(APP.'config'.DS.'database.php.install', APP.'config'.DS.'database.php');

                // open database.php file
                App::import('Core', 'File');
                $file = new File(APP.'config'.DS.'database.php', true);
                $content = $file->read();

                // write database.php file
                $content = str_replace('{default_host}', $this->data['Install']['host'], $content);
                $content = str_replace('{default_login}', $this->data['Install']['login'], $content);
                $content = str_replace('{default_password}', $this->data['Install']['password'], $content);
                $content = str_replace('{default_database}', $this->data['Install']['database'], $content);
                if($file->write($content) ) {
                    $this->redirect(array('action' => 'data'));
                } else {
                    $this->Session->setFlash(__('Could not write database.php file.', true));
                }
            } else {
                $this->Session->setFlash(__('Could not connect to database.', true));
            }
        }
    }
/**
 * Step 2: insert required data
 *
 * @return void
 */
    function data() {
        $this->set('title_for_layout', __('Step 2: Run SQL', true));
        if (isset($this->params['named']['run'])) {
            App::import('Core', 'File');
            App::import('Model', 'ConnectionManager');
            $db = ConnectionManager::getDataSource('default');

            if(!$db->isConnected()) {
                $this->Session->setFlash(__('Could not connect to database.', true));
            } else {
                $this->__executeSQLScript($db, CONFIGS.'sql'.DS.'croogo.sql');
                $this->__executeSQLScript($db, CONFIGS.'sql'.DS.'croogo_data.sql');

                $this->redirect(array('action' => 'finish'));
            }
        }
    }
/**
 * Step 3: finish
 *
 * Remind the user to delete 'install' plugin, move the bootstrap and settings.yml files into place
 * If the croogo bootstrap file exists this plugin is disabled
 *
 * @return void
 */
	function finish() {
		$this->set('title_for_layout', __('Installation completed successfully', true));
		if (isset($this->params['named']['delete'])) {
			App::import('Core', 'Folder');
			$this->folder = new Folder;
			if ($this->folder->delete(APP.'plugins'.DS.'install')) {
				$this->Session->setFlash(__('Installataion files deleted successfully.', true));
				$this->redirect('/');
			} else {
				$this->Session->setFlash(__('Could not delete installation files.', true));
			}
		}
		copy(APP.'config'.DS.'croogo_bootstrap.php.install', APP.'config'.DS.'croogo_bootstrap.php');
		copy(APP.'config'.DS.'settings.yml.install', APP.'config'.DS.'settings.yml');
	}
/**
 * Execute SQL file
 *
 * @link   http://cakebaker.42dh.com/2007/04/16/writing-an-installer-for-your-cakephp-application/
 * @param  object $db Database
 * @param  string $fileName sql file
 * @return void
 */
    function __executeSQLScript($db, $fileName) {
        $statements = file_get_contents($fileName);
        $statements = explode(';', $statements);

        foreach ($statements as $statement) {
            if (trim($statement) != '') {
                $db->query($statement);
            }
        }
    }

}
?>