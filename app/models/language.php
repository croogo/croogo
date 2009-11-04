<?php
/**
 * Language
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
class Language extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    var $name = 'Language';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    var $actsAs = array(
        'Ordered' => array('field' => 'weight', 'foreign_key' => null),
    );
/**
 * Validation
 *
 * @var array
 * @access public
 */
    var $validate = array(
        'alias' => array(
            'rule' => 'isUnique',
            'message' => 'Alias is already in use.',
        ),
    );

}
?>