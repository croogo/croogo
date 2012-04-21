<?php
/**
 * Comment
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
class Comment extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Comment';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Tree',
		'Containable',
		'Cached' => array(
			'prefix' => array(
				'comment_',
				'nodes_',
				'node_',
			),
		),
	);

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'body' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
		),
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'This field cannot be left blank.',
		),
		'email' => array(
			'rule' => 'email',
			'required' => true,
			'message' => 'Please enter a valid email address.',
		),
	);

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Node' => array(
			'counterCache' => true,
			'counterScope' => array('Comment.status' => 1),
		),
		'User',
	);

}
