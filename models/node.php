<?php
/**
 * Node
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
class Node extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    public $name = 'Node';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    public $actsAs = array(
        'Containable',
        'Tree',
        'Encoder',
        'Meta',
        'Url',
        'Cached' => array(
            'prefix' => array(
                'node_',
                'nodes_',
                'croogo_nodes_',
            ),
        ),
    );
/**
 * Node type
 *
 * If the Model is associated to Node model, this variable holds the Node type value
 *
 * @var string
 * @access public
 */
    public $type = null;
/**
 * Guid
 *
 * @var string
 * @access public
 */
    public $guid = null;
/**
 * Validation
 *
 * @var array
 * @access public
 */
    public $validate = array(
        'title' => array(
            'rule' => 'notEmpty',
            'message' => 'This field cannot be left blank.',
        ),
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
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ),
    );
/**
 * Model associations: hasMany
 *
 * @var array
 * @access public
 */
    public $hasMany = array(
        'Comment' => array(
            'className' => 'Comment',
            'foreignKey' => 'node_id',
            'dependent' => false,
            'conditions' => array('Comment.status' => 1),
            'fields' => '',
            'order' => '',
            'limit' => '5',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => '',
        ),
        'Meta' => array(
            'className' => 'Meta',
            'foreignKey' => 'foreign_key',
            'dependent' => false,
            'conditions' => array('Meta.model' => 'Node'),
            'fields' => '',
            'order' => 'Meta.key ASC',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => '',
        ),
    );
/**
 * Model associations: hasAndBelongsToMany
 *
 * @var array
 * @access public
 */
    public $hasAndBelongsToMany = array(
        'Taxonomy' => array(
            'className' => 'Taxonomy',
            'with' => 'NodesTaxonomy',
            'joinTable' => 'nodes_taxonomies',
            'foreignKey' => 'node_id',
            'associationForeignKey' => 'taxonomy_id',
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
 * beforeFind callback
 *
 * @param array $q
 * @return array
 */
    public function beforeFind($q) {
        if($this->type != null) {
            $q['conditions']['Node.type'] = $this->type;
        }
        return $q;
    }
/**
 * beforeSave callback
 *
 * @return boolean
 */
    public function beforeSave() {
        if ($this->type != null) {
            $this->data['Node']['type'] = $this->type;
        }
        $this->__cacheTerms();

        return true;
    }
/**
 * Caches Term in Node.terms field
 *
 * @return void
 */
    public function __cacheTerms() {
        if (isset($this->data['Taxonomy']['Taxonomy']) && count($this->data['Taxonomy']['Taxonomy']) > 0) {
            $taxonomyIds = $this->data['Taxonomy']['Taxonomy'];
            $taxonomies = $this->Taxonomy->find('all', array(
                'conditions' => array(
                    'Taxonomy.id' => $taxonomyIds,
                ),
            ));
            $terms = Set::combine($taxonomies, '{n}.Term.id', '{n}.Term.slug');
            $this->data['Node']['terms'] = $this->encodeData($terms, array(
                'trim' => false,
                'json' => true,
            ));
        }
    }
}
?>