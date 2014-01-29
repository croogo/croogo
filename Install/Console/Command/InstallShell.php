<?php

App::uses('AppShell', 'Console/Command');
App::uses('InstallManager', 'Install.Lib');
App::uses('Install', 'Install.Model');
App::uses('ComponentCollection', 'Controller');
App::uses('AuthComponent', 'Controller/Component');

/**
 * Install Shell
 *
 * @category Shell
 * @package  Croogo.Install.Console.Command
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallShell extends AppShell {

/**
 * Display help/options
 */
	public function getOptionParser() {
		$drivers = array('Mysql', 'Postgres', 'Sqlite', 'Sqlserver');
		$parser = parent::getOptionParser();
		$parser->description(__d('croogo', 'Install Utilities'))
			->addSubcommand('main', array(
				'help' => 'Generate croogo.php, database.php, settings.json and create admin user.',
				'parser' => array(
					'description' => 'Generate croogo.php, database.php, settings.json and create admin user.',
				)
			))
			->addSubcommand('setup_acos', array(
				'help' => 'Setup default ACOs for roles',
				'parser' => array(
					'description' => 'Generate default role settings during release',
				)
			))
			->addOption('datasource', array(
				'help' => 'Database Driver',
				'short' => 'd',
				'required' => true,
				'options' => $drivers,
			))
			->addOption('host', array(
				'help' => 'Database Host',
				'short' => 'h',
				'required' => true,
			))
			->addOption('login', array(
				'help' => 'Database User',
				'short' => 'l',
				'required' => true,
			))
			->addOption('password', array(
				'help' => 'Database Password',
				'short' => 'p',
				'required' => true,
			))
			->addOption('database-name', array(
				'help' => 'Database Name',
				'short' => 'n',
				'required' => true,
			))
			->addOption('port', array(
				'help' => 'Database Port',
				'short' => 't',
				'required' => true,
			))
			->addOption('prefix', array(
				'help' => 'Table Prefix',
				'short' => 'x',
				'required' => true,
			))
			->addArgument('admin-user', array(
				'help' => 'Admin username',
			))
			->addArgument('admin-password', array(
				'help' => 'Admin password',
			));
		return $parser;
	}

/**
 * Convenient wrapper for Shell::in() that gets the default from CLI options
 */
	protected function _in($prompt, $options = null, $default = null, $argument = null) {
		if (isset($this->params[$argument])) {
			return $this->params[$argument];
		}
		return $this->in($prompt, $options, $default);
	}

/**
 * Convenient wrapper for Shell::in() that gets the default from CLI argument
 */
	protected function _args($prompt, $options = null, $default = null, $index = null) {
		if (isset($this->args[$index])) {
			return $this->args[$index];
		}
		return $this->in($prompt, $options, $default);
	}

	public function main() {
		$this->out();
		$this->out('Database settings:');
		$install['Install']['datasource'] = $this->_in(__d('croogo', 'DataSource'), array(
			'Mysql',
			'Sqlite',
			'Postgres',
			'Sqlserver'
		), 'Mysql', 'datasource');
		$install['Install']['datasource'] = 'Database/' . $install['Install']['datasource'];
		$install['Install']['host'] = $this->_in(__d('croogo', 'Host'), null, 'localhost', 'host');
		$install['Install']['login'] = $this->_in(__d('croogo', 'Login'), null, 'root', 'login');
		$install['Install']['password'] = $this->_in(__d('croogo', 'Password'), null, '', 'password');
		$install['Install']['database'] = $this->_in(__d('croogo', 'Database'), null, 'croogo', 'database-name');
		$install['Install']['prefix'] = $this->_in(__d('croogo', 'Prefix'), null, '', 'prefix');
		$install['Install']['port'] = $this->_in(__d('croogo', 'Port'), null, null, 'port');

		$InstallManager = new InstallManager();
		$InstallManager->createDatabaseFile($install);

		$this->out('Setting up database objects. Please wait...');
		$Install = ClassRegistry::init('Install.Install');
		try {
			$result = $Install->setupDatabase();
			if ($result !== true) {
				$this->err($result);
				return $this->_stop();
			}
		} catch (Exception $e) {
			$this->err($e->getMessage());
			$this->err('Please verify you have the correct credentials');
			return $this->_stop();
		}
		$InstallManager->createCroogoFile();

		$this->out();
		if (empty($this->args)) {
			$this->out('Create Admin user:');
		}

		do {
			$username = $this->_args(__d('croogo', 'Username'), null, null, 0);
			if (empty($username)) {
				$this->err('Username must not be empty');
			}
		} while (empty($username));

		do {
			$password = $this->_args(__d('croogo', 'Password'), null, null, 1);
			if (empty($this->args)) {
				$verify = $this->_in(__d('croogo', 'Verify Password'), null, null, 1);
				$passwordsMatched = $password == $verify;

				if (!$passwordsMatched) {
					$this->err('Passwords do not match');
				}
			} else {
				$passwordsMatched = true;
			}
			if (empty($password)) {
				$this->err('Password must not be empty');
			}
		} while (empty($password) || !$passwordsMatched);

		$user['User']['username'] = $username;
		$user['User']['password'] = $password;

		$Install->addAdminUser($user);
		$InstallManager->createSettingsFile();
		$InstallManager->installCompleted();

		$this->out();
		$this->success('Congratulations, Croogo has been installed successfully.');
	}

	public function setup_acos() {
		$Role = ClassRegistry::init('Users.Role');
		$Role->Behaviors->attach('Croogo.Aliasable');
		$public = $Role->byAlias('public');
		$registered = $Role->byAlias('registered');

		$Permission = ClassRegistry::init('Acl.AclPermission');

		$setup = array(
			'controllers/Comments/Comments/index' => array($public),
			'controllers/Comments/Comments/add' => array($public),
			'controllers/Comments/Comments/delete' => array($registered),
			'controllers/Contacts/Contacts/view' => array($public),
			'controllers/Nodes/Nodes/index' => array($public),
			'controllers/Nodes/Nodes/term' => array($public),
			'controllers/Nodes/Nodes/promoted' => array($public),
			'controllers/Nodes/Nodes/search' => array($public),
			'controllers/Nodes/Nodes/view' => array($public),
			'controllers/Users/Users/index' => array($registered),
			'controllers/Users/Users/add' => array($public),
			'controllers/Users/Users/activate' => array($public),
			'controllers/Users/Users/edit' => array($registered),
			'controllers/Users/Users/forgot' => array($public),
			'controllers/Users/Users/reset' => array($public),
			'controllers/Users/Users/login' => array($public),
			'controllers/Users/Users/logout' => array($registered),
			'controllers/Users/Users/admin_logout' => array($registered),
			'controllers/Users/Users/view' => array($registered),
		);

		foreach ($setup as $aco => $roles) {
			foreach ($roles as $roleId) {
				$aro = array(
					'model' => 'Role',
					'foreign_key' => $roleId,
				);
				if ($Permission->allow($aro, $aco)) {
					$this->success(__d('croogo', 'Permission %s granted to %s', $aco, $Role->byId($roleId)));
				}
			}
		}
	}

}
