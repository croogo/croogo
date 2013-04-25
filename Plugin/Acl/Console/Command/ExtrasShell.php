<?php
/**
 * Acl Extras Shell.
 *
 * Enhances the existing Acl Shell with a few handy functions
 *
 * Copyright 2008, Mark Story.
 * 694B The Queensway
 * toronto, ontario M8Y 1K9
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2008-2010, Mark Story.
 * @link http://mark-story.com
 * @author Mark Story <mark@mark-story.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::uses('AclExtras', 'Acl.Lib');

/**
 * Shell for ACO extras
 *
 * @package     Croogo.Acl.Console.Command
 * @subpackage  Acl.Console.Command
 */
class ExtrasShell extends Shell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * AclExtras instance
 */
	public $AclExtras;

/**
 * Constructor
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->AclExtras = new AclExtras();
	}

/**
 * Start up And load Acl Component / Aco model
 *
 * @return void
 **/
	public function startup() {
		parent::startup();
		$this->AclExtras->startup();
		$this->AclExtras->Shell = $this;
	}

/**
 * Sync the ACO table
 *
 * @return void
 **/
	public function aco_sync() {
		$this->AclExtras->aco_sync($this->params);
	}

/**
 * Sync the ACO table for contents
 *
 * @return void
 */
	public function aco_sync_contents() {
		$this->AclExtras->args = $this->args;
		$this->AclExtras->aco_update_contents($this->params);
	}

/**
 * Updates the Aco Tree with new controller actions.
 *
 * @return void
 **/
	public function aco_update() {
		$this->AclExtras->aco_update($this->params);
		return true;
	}

	public function getOptionParser() {
		$plugin = array(
			'short' => 'p',
			'help' => __d('croogo', 'Plugin to process'),
		);
		return parent::getOptionParser()
			->description(__d('croogo', "Better manage, and easily synchronize you application's ACO tree"))
			->addSubcommand('aco_update', array(
				'parser' => array(
					'options' => compact('plugin'),
				),
				'help' => __d('croogo', 'Add new ACOs for new controllers and actions. Does not remove nodes from the ACO table.')
			))
			->addSubcommand('aco_sync', array(
				'parser' => array(
					'options' => compact('plugin'),
				),
				'help' => __d('croogo', 'Perform a full sync on the ACO table.' .
					'Will create new ACOs or missing controllers and actions.' .
					'Will also remove orphaned entries that no longer have a matching controller/action')
			))
			->addSubcommand('aco_sync_contents', array(
				'help' => __d('croogo', 'Perform a full content sync on the ACO table.' .
					'Will create new ACOs or missing contents.' .
					'Will also remove orphaned entries that no longer have a matching contents'),
				'parser' => array(
					'arguments' => array(
						'model' => array(
							'required' => true,
							'help' => __d('croogo', 'The content model name '),
						)
					),
				),
			))
			->addSubcommand('verify', array(
				'help' => __d('croogo', 'Verify the tree structure of either your Aco or Aro Trees'),
				'parser' => array(
					'arguments' => array(
						'type' => array(
							'required' => true,
							'help' => __d('croogo', 'The type of tree to verify'),
							'choices' => array('aco', 'aro')
						)
					)
				)
			))
			->addSubcommand('recover', array(
				'help' => __d('croogo', 'Recover a corrupted Tree'),
				'parser' => array(
					'arguments' => array(
						'type' => array(
							'required' => true,
							'help' => __d('croogo', 'The type of tree to recover'),
							'choices' => array('aco', 'aro')
						)
					)
				)
			));
	}

/**
 * Verify a Acl Tree
 *
 * @param string $type The type of Acl Node to verify
 * @access public
 * @return void
 */
	public function verify() {
		$this->AclExtras->args = $this->args;
		return $this->AclExtras->verify();
	}
/**
 * Recover an Acl Tree
 *
 * @param string $type The Type of Acl Node to recover
 * @access public
 * @return void
 */
	public function recover() {
		$this->AclExtras->args = $this->args;
		$this->AclExtras->recover();
	}

}
