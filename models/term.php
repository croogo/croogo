<?php
/**
 * Term
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
class Term extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    public $name = 'Term';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    public $actsAs = array(
        'Tree',
        'Cached' => array(
            'prefix' => array(
                'term_',
                'croogo_vocabularies_',
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
        'slug' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This slug has already been taken.',
            ),
            'minLength' => array(
                'rule' => array('minLength', 1),
                'message' => 'Slug cannot be empty.',
            ),
        ),
    );
/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
    public $belongsTo = array(
        'Vocabulary' => array(
            'counterCache' => true,
        ),
    );

}
?>