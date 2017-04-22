<?php

/**
 * Translate Activation
 *
 * @package  Croogo.Translate
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
namespace Croogo\Translate\Config;

use Cake\ORM\TableRegistry;
use Croogo\Core\Plugin;

class PluginActivation
{

/**
 * onActivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeActivation(&$controller)
    {
        return true;
    }

/**
 * Called after activating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onActivation(&$controller)
    {
        $Acos = TableRegistry::get('Croogo/Acl.Acos');
        $Acos->addAco('Croogo\Translate/Admin/Translate/index');
        $Acos->addAco('Croogo\Translate/Admin/Translate/edit');
        $Acos->addAco('Croogo\Translate/Admin/Translate/delete');
        $CroogoPlugin = new Plugin();
        $CroogoPlugin->migrate('Croogo/Translate');
    }

/**
 * onDeactivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeDeactivation(&$controller)
    {
        return true;
    }

/**
 * Called after deactivating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onDeactivation(&$controller)
    {
        $Acos = TableRegistry::get('Croogo/Acl.Acos');
        $Acos->removeAco('Croogo\Translate');
    }
}
