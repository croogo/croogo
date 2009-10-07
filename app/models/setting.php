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

    function afterSave() {
        $settings = $this->find('all', array('fields' => array('Setting.key', 'Setting.value')));
        Cache::write("settings", $settings);
        $this->writeConfiguration();
    }

    function afterDelete() {
        $settings = $this->find('all', array('fields' => array('Setting.key', 'Setting.value')));
        Cache::write("settings", $settings);
        $this->writeConfiguration();
    }

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