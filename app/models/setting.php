<?php
/**
 * Setting
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
class Setting extends AppModel {
/**
 * Model name
 *
 * @var string
 * @access public
 */
    var $name = 'Setting';
/**
 * Use cache for Configuration
 *
 * if true, it will cache the model find when writing configuration for global use.
 * see Setting::writeConfiguration()
 *
 * @var boolean
 * @access public
 */
    var $cacheConfiguration = true;
/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    var $actsAs = array('Ordered' => array(
            'field'         => 'weight',
            'foreign_key'     => false
        ));
/**
 * afterSave callback
 *
 * @return void
 */
    function afterSave() {
        $settings = $this->find('all', array('fields' => array('Setting.key', 'Setting.value')));
        Cache::write("settings", $settings);
        $this->writeConfiguration();
    }
/**
 * afterDelete callback
 *
 * @return void
 */
    function afterDelete() {
        $settings = $this->find('all', array('fields' => array('Setting.key', 'Setting.value')));
        Cache::write("settings", $settings);
        $this->writeConfiguration();
    }
/**
 * Creates a new record with key/value pair if key does not exist.
 *
 * @param string $key
 * @param string $value
 * @param array $options
 * @return boolean
 */
    function write($key, $value, $options = array()) {
        $_options = array(
            'editable' => 0,
        );
        $options = array_merge($_options, $options);

        $setting = $this->findByKey($key);
        if (isset($setting['Setting']['id'])) {
            $setting['Setting']['id'] = $setting['Setting']['id'];
            $setting['Setting']['value'] = $value;
            $setting['Setting']['editable'] = $options['editable'];
        } else {
            $setting = array();
            $setting['key'] = $key;
            $setting['value'] = $value;
            $setting['editable'] = $options['editable'];
        }

        $this->id = false;
        if ($this->save($setting)) {
            Configure::write($key, $value);
            return true;
        } else {
            return false;
        }
    }
/**
 * Deletes setting record for given key
 *
 * @param string $key
 * @return boolean
 */
    function deleteKey($key) {
        $setting = $this->findByKey($key);
        if (isset($setting['Setting']['id']) &&
            $this->delete($setting['Setting']['id'])) {
            return true;
        }
        return false;
    }
/**
 * All key/value pairs are made accessible from Configure class
 *
 * @return void
 */
    function writeConfiguration() {
        /*if( $this->useCache == false || ($settings = Cache::read("settings")) === false ) {
            $settings = $this->find('all', array('fields' => array('Setting.key', 'Setting.value')));
            Cache::write("settings", $settings);
        }*/

        $settings = $this->find('all', array('fields' => array('Setting.key', 'Setting.value')));
        foreach($settings AS $setting) {
            Configure::write($setting['Setting']['key'], $setting['Setting']['value']);
        }
    }
}
?>