<?php

App::uses('InstallManager','Install.Lib');
App::uses('Install','Install.Model');
App::uses('ComponentCollection', 'Controller');
App::uses('AuthComponent','Controller/Component');

/**
 * Install Shell
 *
 * PHP version 5
 *
 * @category Shell
 * @package  Croogo
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
		$parser->description(__('Install Utilities')
			)->addSubcommand('main', array(
				'help' => 'Generate croogo.php, database.php, settings.json and create admin user.',
				'parser' => array(
					'description' => 'Generate croogo.php, database.php, settings.json and create admin user.',
					),
				)
			);
		return $parser;
	}


	public function main() {
		$this->out();
		$this->out('Database settings:');
		$install['Install']['datasource'] = $this->in(__('DataSource'), array(
			'Mysql',
			'Sqlite',
			'Postgres',
			'Sqlserver'
		), 'Mysql');
		$install['Install']['datasource'] = 'Database/' . $install['Install']['datasource'];
		$install['Install']['host'] = $this->in(__('Host'), null, 'localhost');
		$install['Install']['login'] = $this->in(__('Login'), null, 'root');
		$install['Install']['password'] = $this->in(__('Password'), null, '');
		$install['Install']['database'] = $this->in(__('Database'), null, 'croogo');
		$install['Install']['prefix'] = $this->in(__('Prefix'), null, '');
		$install['Install']['port'] = $this->in(__('Port'), null, null);

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
			$username = $this->in(__('Username'), null, null);
			if (empty($username)) {
				$this->err('Username must not be empty');
			}
		} while (empty($username));

		do {
			$password = $this->in(__('Password'));
			$password2 = $this->in(__('Verify Password'));
			$passwordsMatched = $password == $password2;
			if (!$passwordsMatched) {
				$this->err('Passwords do not match');
			}
			if (empty($password)) {
				$this->err('Password must not be empty');
			}
		} while (empty($password) || !$passwordsMatched);

		$user['User']['username'] = $username;
		$user['User']['password'] = AuthComponent::password($password);
		
		$Install->addAdminUser($user);
		$InstallManager->createSettingsFile();

		$this->out();
		$this->success('Congratulations, Croogo has been installed successfully.');
	}
}
