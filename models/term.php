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
        'Cached' => array(
            'prefix' => array(
                'term_',
                'node_',
                'nodes_',
                'croogo_nodes_',
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
 * Model associations: hasAndBelongsToMany
 *
 * @var array
 * @access public
 */
    public $hasAndBelongsToMany = array(
        'Vocabulary' => array(
            'className' => 'Vocabulary',
            'with' => 'Taxonomy',
            'joinTable' => 'taxonomy',
            'foreignKey' => 'term_id',
            'associationForeignKey' => 'vocabulary_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => '',
        ),
    );
/**
 * Save Term and return ID.
 * If another Term with same slug exists, return ID of that Term without saving.
 *
 * @param  array $data
 * @return integer
 */
    public function saveAndGetId($data) {
        $term = $this->find('first', array(
            'conditions' => array(
                'Term.slug' => $data['slug'],
            ),
        ));
        if (isset($term['Term']['id'])) {
            return $term['Term']['id'];
        }

        $this->id = false;
        if ($this->save($data)) {
            return $this->id;
        }
        return false;
    }
}
?>