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
 * @package  Settings
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
				'help' => __('Displays setting values'),
				'parser' => array(
					'arguments' => array(
						'key' => array(
							'help' => __('Setting key'),
							'required' => false,
						),
					),
					'options' => array(
						'all' => array(
							'help' => __('List all settings'),
							'short' => 'a',
							'boolean' => true,
						)
					),
				),
			))
			->addSubcommand('write', array(
				'help' => __('Write setting value for a given key'),
				'parser' => array(
					'arguments' => array(
						'key' => array(
							'help' => __('Setting key'),
							'required' => true,
						),
						'value' => array(
							'help' => __('Setting value'),
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
							'choices' => array('text', 'textarea', 'checkbox', 'multiple'),
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
				'help' => __('Delete setting based on key'),
				'parser' => array(
					'arguments' => array(
						'key' => array(
							'help' => __('Setting key'),
							'required' => true,
						),
					),
				)
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
			$this->out(__("    %-30s: %s", $data['Setting']['key'], $data['Setting']['value']));
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
		$this->out(__('Updating %s', $key), 2);
		$ask = __("Confirm update");
		if ($setting || $this->params['create']) {
			$text = '-';
			if ($setting) {
				$text = __('- %s', $setting['Setting']['value']);
			}
			$this->warn($text);
			$this->success(__('+ %s', $val));
			if ('y' == $this->in($ask, array('y', 'n'), 'n')) {
				$keys = array(
					'title' => null, 'description' => null,
					'input_type' => null, 'editable' => null, 'params' => null,
				);
				$options = array_intersect_key($this->params, $keys);
				$this->Setting->write($key, $val, $options);
				$this->success(__('Setting updated'));
			} else {
				$this->warn(__('Cancelled'));
			}
		} else {
			$this->warn(__('Key: %s not found', $key));
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
		$this->out(__('Deleting %s', $key), 2);
		$ask = __('Delete?');
		if ($setting) {
			if ('y' == $this->in($ask, array('y', 'n'), 'n')) {
				$this->Setting->deleteKey($setting['Setting']['key']);
				$this->success(__('Setting deleted'));
			} else {
				$this->warn(__('Cancelled'));
			}
		} else {
			$this->warn(__('Key: %s not found', $key));
		}
	}

}
