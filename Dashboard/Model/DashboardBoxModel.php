<?php

App::uses('DashboardAppModel', 'Dashboard.Model');

/**
 * Dashboard Box Model
 *
 * @category Dashboard.Model
 * @package  Croogo.Dashboard.Model
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardBoxModel extends DashboardAppModel {

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);
}
