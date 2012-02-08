<?php
/**
 * Role
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
class Role extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    public $name = 'Role';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    public $actsAs = array(
        'Acl' => array(
            'type' => 'requester',
        ),
    );
/**
 * Validation
 *
 * @var array
 * @access public
 */
    public $validate = array(
        'title' => array(
            'rule' => array('minLength', 1),
            'message' => 'Title cannot be empty.',
        ),
        'alias' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This alias has already been taken.',
            ),
            'minLength' => array(
                'rule' => array('minLength', 1),
                'message' => 'Alias cannot be empty.',
            ),
        ),
    );

    public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        } else {
            $id = $this->id ? $this->id : $this->data['Role']['id'];
            $aro = $this->Aro->find('first', array(
                'conditions' => array(
                    'model' => $this->alias,
                    'foreign_key' => $id,
                    )
                ));
            if (empty($aro['Aro']['foreign_key'])) {
                return null;
            } else {
                return array('Role' => array('id' => $aro['Aro']['foreign_key']));
            }
        }
    }

}
?>