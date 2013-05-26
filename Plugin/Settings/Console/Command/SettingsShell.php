<?php

/**
 * Settings Shell
 *
 * Manipulates Settings via CLI
 *	./Console/croogo settings.settings read -a
 *	./Console/croogo settings.settings delete Some.key
 *	./Console/croogo settings.settings write Some.key newvalue
 *	./Console/croogo settings.settings write Some.key newvalue -create
 *
 * @category Shell
 * @package  Croogo.Settings.Console.Command
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */

class SettingsShell extends AppShell {

/**
 * models
 */
	public $uses = array('Settings.Setting');

/**
 * getOptionParser
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->description('Croogo Settings utility')
			->addSubCommand('read', array(
				'help' => __d('croogo', 'Displays setting values'),
				'parser' => array(
					'arguments' => array(
						'key' => array(
							'help' => __d('croogo', 'Setting key'),
							'required' => false,
						),
					),
					'options' => array(
						'all' => array(
							'help' => __d('croogo', 'List all settings'),
							'short' => 'a',
							'boolean' => true,
						)
					),
				),
			))
			->addSubcommand('write', array(
				'help' => __d('croogo', 'Write setting value for a given key'),
				'parser' => array(
					'arguments' => array(
						'key' => array(
							'help' => __d('croogo', 'Setting key'),
							'required' => true,
						),
						'value' => array(
							'help' => __d('croogo', 'Setting value'),
							'required' => true,
						),
					),
					'options' => array(
						'create' => array(
							'boolean' => true,
							'short' => 'c',
						),
						'title' => array(
							'short' => 't',
						),
						'description' => array(
							'short' => 'd',
						),
						'input_type' => array(
							'choices' => array('text', 'textarea', 'checkbox', 'multiple', 'radio'),
							'short' => 'i',
						),
						'editable' => array(
							'short' => 'e',
							'boolean' => true,
						),
						'params' => array(
							'short' => 'p',
						),
					),
				)
			))
			->addSubcommand('delete', array(
				'help' => __d('croogo', 'Delete setting based on key'),
				'parser' => array(
					'arguments' => array(
						'key' => array(
							'help' => __d('croogo', 'Setting key'),
							'required' => true,
						),
					),
				)
			))
			->addSubcommand('update_version_info', array(
				'help' => __d('croogo', 'Update version string from git tag information'),
			));
	}

/**
 * Read setting
 *
 * @param string $key
 * @return void
 */
	public function read() {
		if (empty($this->args)) {
			if ($this->params['all'] === true) {
				$key = null;
			} else {
				$this->out($this->OptionParser->help('get'));
				return;
			}
		} else {
			$key = $this->args[0];
		}
		$settings = $this->Setting->find('all', array(
			'conditions' => array(
				'Setting.key like' => '%' . $key . '%',
			),
			'order' => 'Setting.weight asc',
		));
		$this->out("Settings: ", 2);
		foreach ($settings as $data) {
			$this->out(__d('croogo', "    %-30s: %s", $data['Setting']['key'], $data['Setting']['value']));
		}
		$this->out();
	}

/**
 * Write setting
 *
 * @param string $key
 * @param string $val
 * @return void
 */
	public function write() {
		$key = $this->args[0];
		$val = $this->args[1];
		$setting = $this->Setting->find('first', array(
			'fields' => array('id', 'key', 'value'),
			'conditions' => array(
				'Setting.key' => $key,
			),
		));
		$this->out(__d('croogo', 'Updating %s', $key), 2);
		$ask = __d('croogo', "Confirm update");
		if ($setting || $this->params['create']) {
			$text = '-';
			if ($setting) {
				$text = __d('croogo', '- %s', $setting['Setting']['value']);
			}
			$this->warn($text);
			$this->success(__d('croogo', '+ %s', $val));
			if ('y' == $this->in($ask, array('y', 'n'), 'n')) {
				$keys = array(
					'title' => null, 'description' => null,
					'input_type' => null, 'editable' => null, 'params' => null,
				);
				$options = array_intersect_key($this->params, $keys);
				$this->Setting->write($key, $val, $options);
				$this->success(__d('croogo', 'Setting updated'));
			} else {
				$this->warn(__d('croogo', 'Cancelled'));
			}
		} else {
			$this->warn(__d('croogo', 'Key: %s not found', $key));
		}
	}

/**
 * Delete setting
 *
 * @param string $key
 * @return void
 */
	public function delete() {
		$key = $this->args[0];
		$setting = $this->Setting->find('first', array(
			'fields' => array('id', 'key', 'value'),
			'conditions' => array(
				'Setting.key' => $key,
			),
		));
		$this->out(__d('croogo', 'Deleting %s', $key), 2);
		$ask = __d('croogo', 'Delete?');
		if ($setting) {
			if ('y' == $this->in($ask, array('y', 'n'), 'n')) {
				$this->Setting->deleteKey($setting['Setting']['key']);
				$this->success(__d('croogo', 'Setting deleted'));
			} else {
				$this->warn(__d('croogo', 'Cancelled'));
			}
		} else {
			$this->warn(__d('croogo', 'Key: %s not found', $key));
		}
	}

/**
 * Update Croogo.version in settings.json
 */
	public function update_version_info() {
		$gitDir = APP . '.git';
		if (!file_exists($gitDir)) {
			$this->err('Git repository not found');
			return false;
		}

		$git = trim(shell_exec('which git'));
		if (empty($git)) {
			$this->err('Git executable not found');
			return false;
		}

		chdir($gitDir);
		$version = trim(shell_exec('git describe --tags'));
		if ($version) {
			$this->runCommand('write', array('write', 'Croogo.version', $version));
		}
	}

}
