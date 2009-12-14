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
    var $name = 'Node';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    var $actsAs = array(
        'Containable',
        'Tree',
        'Encoder',
        'Meta',
        'Url',
        'Acl' => array(
            'type' => 'controlled',
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
    var $type = null;
/**
 * Guid
 *
 * @var string
 * @access public
 */
    var $guid = null;
/**
 * Validation
 *
 * @var array
 * @access public
 */
    var $validate = array(
        'title' => array(
            'rule' => 'notEmpty',
            'message' => 'This field cannot be left blank.',
        ),
        'slug' => array(
            'rule' => 'isUnique',
            'message' => 'This slug has already been taken',
        ),
    );
/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
    var $belongsTo = array(
            'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
            )
    );
/**
 * Model associations: hasMany
 *
 * @var array
 * @access public
 */
    var $hasMany = array(
            'Comment' => array('className' => 'Comment',
                                'foreignKey' => 'node_id',
                                'dependent' => false,
                                'conditions' => array('Comment.status' => 1),
                                'fields' => '',
                                'order' => '',
                                'limit' => '5',
                                'offset' => '',
                                'exclusive' => '',
                                'finderQuery' => '',
                                'counterQuery' => ''
            ),
            'Meta' => array('className' => 'Meta',
                                'foreignKey' => 'foreign_key',
                                'dependent' => false,
                                'conditions' => array('Meta.model' => 'Node'),
                                'fields' => '',
                                'order' => 'Meta.key ASC',
                                'limit' => '',
                                'offset' => '',
                                'exclusive' => '',
                                'finderQuery' => '',
                                'counterQuery' => ''
            )
    );
/**
 * Model associations: hasAndBelongsToMany
 *
 * @var array
 * @access public
 */
    var $hasAndBelongsToMany = array(
            'Term' => array('className' => 'Term',
                        'joinTable' => 'nodes_terms',
                        'foreignKey' => 'node_id',
                        'associationForeignKey' => 'term_id',
                        'unique' => true,
                        'conditions' => '',
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
            )
    );

    function parentNode() {
        return null;
    }

    function beforeFind($q) {
        if($this->type != null) {
            $q['conditions']['Node.type'] = $this->type;
        }
        return $q;
    }

    function beforeSave() {
        if ($this->type != null) {
            $this->data['Node']['type'] = $this->type;
        }
        $this->__cache_terms();

        return true;
    }

    function afterSave($created) {

    }

    function __cache_terms() {
        if (isset($this->data['Term']['Term']) && count($this->data['Term']['Term']) > 0) {
            $termIds = $this->data['Term']['Term'];
            $terms = $this->Term->find('list', array(
                'conditions' => array(
                    'Term.id' => $termIds,
                ),
                'fields' => array(
                    'Term.id',
                    'Term.slug',
                ),
            ));
            $this->data['Node']['terms'] = $this->encodeData($terms, array(
                'trim' => false,
                'json' => true,
            ));
        }
    }
}
?>