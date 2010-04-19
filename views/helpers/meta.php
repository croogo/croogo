<?php
/**
 * Meta Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    public $helpers = array('Html', 'Form');
/**
 * Meta with key/value fields
 *
 * @param string $key (optional) key
 * @param string $value (optional) value
 * @param integer $id (optional) ID of Meta
 * @param array $options (optional) options
 * @return string
 */
    public function field($key = '', $value = null, $id = null, $options = array()) {
        $_options = array(
            'key'   => array(
                'label'   => __('Key', true),
                'value'   => $key,
            ),
            'value' => array(
                'label'   => __('Value', true),
                'value'   => $value,
            ),
        );
        $options = array_merge($_options, $options);
        $uuid = String::uuid();

        $fields  = '';
        if ($id != null) {
            $fields .= $this->Form->input('Meta.'.$uuid.'.id', array('type' => 'hidden', 'value' => $id));
        }
        $fields .= $this->Form->input('Meta.'.$uuid.'.key', $options['key']);
        $fields .= $this->Form->input('Meta.'.$uuid.'.value', $options['value']);
        $fields = $this->Html->tag('div', $fields, array('class' => 'fields'));

        $actions = $this->Html->link(__('Remove', true), '#', array('class' => 'remove-meta', 'rel' => $id), null, null, false);
        $actions = $this->Html->tag('div', $actions, array('class' => 'actions'));

        $output = $this->Html->tag('div', $actions . $fields, array('class' => 'meta'));
        return $output;
    }

}
?>