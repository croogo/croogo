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
			));
		return $parser;
	}

	public function main() {
		$this->out();
		$this->out('Database settings:');
		$install['Install']['datasource'] = $this->in(__d('croogo', 'DataSource'), array(
			'Mysql',
			'Sqlite',
			'Postgres',
			'Sqlserver'
		), 'Mysql');
		$install['Install']['datasource'] = 'Database/' . $install['Install']['datasource'];
		$install['Install']['host'] = $this->in(__d('croogo', 'Host'), null, 'localhost');
		$install['Install']['login'] = $this->in(__d('croogo', 'Login'), null, 'root');
		$install['Install']['password'] = $this->in(__d('croogo', 'Password'), null, '');
		$install['Install']['database'] = $this->in(__d('croogo', 'Database'), null, 'croogo');
		$install['Install']['prefix'] = $this->in(__d('croogo', 'Prefix'), null, '');
		$install['Install']['port'] = $this->in(__d('croogo', 'Port'), null, null);

		$InstallManager = new InstallManager();
		$InstallManager->createDatabaseFile($install);

		$this->out('Setting up database objects. Please wait...');
		$Install = ClassRegistry::init('Install.Install');
		try {
			$Install->setupDatabase();
		} catch (Exception $e) {
			$this->err($e->getMessage());
			$this->err('Please verify you have the correct credentials');
			return $this->_stop();
		}
		$InstallManager->createCroogoFile();

		$this->out();
		$this->out('Create Admin user:');

		do {
			$username = $this->in(__d('croogo', 'Username'), null, null);
			if (empty($username)) {
				$this->err('Username must not be empty');
			}
		} while (empty($username));

		do {
			$password = $this->in(__d('croogo', 'Password'));
			$verify = $this->in(__d('croogo', 'Verify Password'));
			$passwordsMatched = $password == $verify;
			if (!$passwordsMatched) {
				$this->err('Passwords do not match');
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
