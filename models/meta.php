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
    var $name = 'Meta';
/**
 * Table name
 *
 * @var string
 * @access public
 */
    var $useTable = 'meta';
/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
    var $belongsTo = array(
        'Node' => array(
            'className' => 'Node',
            'foreignKey' => 'foreign_key',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ),
    );

}
?>