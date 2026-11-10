<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Acl\Auth;

use Croogo\Core\Auth\BaseAuthorize as ParentAuthorize;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\Utility\Inflector;

/**
 * Base authorization adapter for other adapter of this plugin.
 */
abstract class BaseAuthorize extends ParentAuthorize
{

    /**
     * Default config for authorize objects.
     *
     * - `actionPath` - The path to ACO nodes that contains the nodes for
     *    controllers. Used as a prefix
     *    when calling $this->action();
     * - `actionMap` - Action -> crud mappings. Used by authorization objects that
     *    want to map actions to CRUD roles.
     * - `userModel` - Model name that ARO records can be found under.
     *    Defaults to 'User'.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'actionPath' => null,
        'actionMap' => [
            'index' => 'read',
            'add' => 'create',
            'edit' => 'update',
            'view' => 'read',
            'delete' => 'delete',
            'remove' => 'delete',
        ],
        'userModel' => 'Users',
    ];

    /**
     * Get the action path for a given request. Primarily used by authorize objects
     * that need to get information about the plugin, controller, and action being invoked.
     *
     * @param \Cake\Http\ServerRequest $request The request a path is needed for.
     * @param string $path Path
     * @return string The action path for the given request.
     */
    public function action(ServerRequest $request, $path = '/:plugin/:prefix/:controller/:action')
    {
        $plugin = empty($request->getParam('plugin')) ? null : preg_replace('/\//', '\\', Inflector::camelize($request->getParam('plugin'))) . '/';
        $prefix = empty($request->getParam('prefix')) ? null : Inflector::camelize($request->getParam('prefix')) . '/';
        $path = str_replace(
            [':controller', ':action', ':plugin/', ':prefix/'],
            [Inflector::camelize($request->getParam('controller')), $request->getParam('action'), $plugin, $prefix],
            $this->_config['actionPath'] . $path
        );
        $path = str_replace('//', '/', $path);

        return trim($path, '/');
    }

    /**
     * Maps crud actions to actual action names. Used to modify or get the current
     * mapped actions.
     *
     * Create additional mappings for a standard CRUD operation:
     *
     * {{{
     * $this->Auth->mapActions(['create' => ['add', 'register']);
     * }}}
     *
     * Or equivalently:
     *
     * {{{
     * $this->Auth->mapActions(['register' => 'create', 'add' => 'create']);
     * }}}
     *
     * Create mappings for custom CRUD operations:
     *
     * {{{
     * $this->Auth->mapActions([range' => 'search']);
     * }}}
     *
     * You can use the custom CRUD operations to create additional generic
     * permissions that behave like CRUD operations. Doing this will require
     * additional columns on the permissions lookup. For example if one wanted an
     * additional search CRUD operation one would create and additional column
     * '_search' in the aros_acos table. One could create a custom admin CRUD
     * operation for administration functions similarly if needed.
     *
     * @param array $map Either an array of mappings, or undefined to get current values.
     * @return mixed Either the current mappings or null when setting.
     * @see AuthComponent::mapActions()
     */
    public function mapActions(array $map = [])
    {
        if (empty($map)) {
            return $this->_config['actionMap'];
        }
        foreach ($map as $action => $type) {
            if (is_array($type)) {
                foreach ($type as $typedAction) {
                    $this->_config['actionMap'][$typedAction] = $action;
                }
            } else {
                $this->_config['actionMap'][$action] = $type;
            }
        }
    }
}
