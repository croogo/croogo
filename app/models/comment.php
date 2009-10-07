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
    var $name = 'Comment';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    var $actsAs = array(
        'Tree',
        'Containable',
    );
/**
 * Validation
 *
 * @var array
 * @access public
 */
    var $validate = array(
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
    var $belongsTo = array(
        'Node' => array(
            'counterCache' => true,
            'counterScope' => array('Comment.status' => 1),
        ),
        'User',
    );

}
?>