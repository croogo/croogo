<?php
/* SVN FILE: $Id$ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
    require_once APP.'config'.DS.'croogo_router.php';
    CroogoRouter::plugins();
    Router::parseExtensions('json', 'rss');

    // Installer
    if (!file_exists(APP.'config'.DS.'database.php')) {
        CroogoRouter::connect('/', array('plugin' => 'install' ,'controller' => 'install'));
    }

    // Basic
    CroogoRouter::connect('/', array('controller' => 'nodes', 'action' => 'promoted'));
    CroogoRouter::connect('/promoted/*', array('controller' => 'nodes', 'action' => 'promoted'));
    CroogoRouter::connect('/admin', array('admin' => true, 'controller' => 'settings', 'action' => 'dashboard'));
    CroogoRouter::connect('/search/*', array('controller' => 'nodes', 'action' => 'search'));

    // Blog
    CroogoRouter::connect('/blog', array('controller' => 'nodes', 'action' => 'index', 'type' => 'blog'));
    CroogoRouter::connect('/blog/archives/*', array('controller' => 'nodes', 'action' => 'index', 'type' => 'blog'));
    CroogoRouter::connect('/blog/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'blog'));
    CroogoRouter::connect('/blog/term/:slug/*', array('controller' => 'nodes', 'action' => 'term', 'type' => 'blog'));

    // Node
    CroogoRouter::connect('/node', array('controller' => 'nodes', 'action' => 'index', 'type' => 'node'));
    CroogoRouter::connect('/node/archives/*', array('controller' => 'nodes', 'action' => 'index', 'type' => 'node'));
    CroogoRouter::connect('/node/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'node'));
    CroogoRouter::connect('/node/term/:slug/*', array('controller' => 'nodes', 'action' => 'term', 'type' => 'node'));

    // Page
    CroogoRouter::connect('/about', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page', 'slug' => 'about'));
    CroogoRouter::connect('/page/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page'));

    // Users
    CroogoRouter::connect('/register', array('controller' => 'users', 'action' => 'add'));
    CroogoRouter::connect('/user/:username', array('controller' => 'users', 'action' => 'view'), array('pass' => array('username')));

    // Contact
    CroogoRouter::connect('/contact', array('controller' => 'contacts', 'action' => 'view', 'contact'));
?>