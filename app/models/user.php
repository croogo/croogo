<?php
/**
 * User
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
class User extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    var $name = 'User';
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    var $actsAs = array(
        'Acl' => array('type' => 'requester'),
        /*'Url' => array(
            'url' => array('controller' => 'users', 'action' => 'view'),
            'fields' => array('username'),
        ),*/
    );
/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
    var $belongsTo = array('Role');

    function beforeSave() {
        // activation_key
        if (!isset($this->data['User']['id'])) {
            $this->data['User']['activation_key'] = rand(10000, 99999);
        }
        
        return true;
    }

    function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        $data = $this->data;
        if (empty($this->data)) {
            $data = $this->read();
        }
        if (!$data['User']['role_id']) {
            return null;
        } else {
            return array('Role' => array('id' => $data['User']['role_id']));
        }
    }


}
?>