<?php

App::uses('DashboardsAppModel', 'Dashboards.Model');

/**
 * Dashboard Model
 *
 * @category Dashboards.Model
 * @package  Croogo.Dashboards.Model
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsDashboard extends DashboardsAppModel {

/**
 * Physical table name
 */
	public $useTable = 'dashboards';

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
