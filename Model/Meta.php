<?php
/**
 * Meta
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Meta extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Meta';

/**
 * Table name
 *
 * @var string
 * @access public
 */
	public $useTable = 'meta';

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Node' => array(
			'className' => 'Node',
			'foreignKey' => 'foreign_key',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);

}
