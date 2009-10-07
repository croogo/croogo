<?php
/* SVN FILE: $Id: routes.php 7945 2008-12-19 02:16:01Z gwoo $ */
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
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
    Router::parseExtensions('json', 'rss');

    // Basic
    Router::connect('/', array('controller' => 'nodes', 'action' => 'promoted'));
    Router::connect('/promoted/*', array('controller' => 'nodes', 'action' => 'promoted'));
    Router::connect('/admin', array('admin' => true, 'controller' => 'settings', 'action' => 'dashboard'));

    // Blog
    Router::connect('/blog', array('controller' => 'nodes', 'action' => 'index', 'type' => 'blog'));
    Router::connect('/blog/archives/*', array('controller' => 'nodes', 'action' => 'index', 'type' => 'blog'));
    Router::connect('/blog/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'blog'));
    Router::connect('/blog/term/:slug/*', array('controller' => 'nodes', 'action' => 'term', 'type' => 'blog'));

    // Node
    Router::connect('/node', array('controller' => 'nodes', 'action' => 'index', 'type' => 'node'));
    Router::connect('/node/archives/*', array('controller' => 'nodes', 'action' => 'index', 'type' => 'node'));
    Router::connect('/node/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'node'));
    Router::connect('/node/term/:slug/*', array('controller' => 'nodes', 'action' => 'term', 'type' => 'node'));

    // Page
    Router::connect('/page/:slug', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page'));
    Router::connect('/about', array('controller' => 'nodes', 'action' => 'view', 'type' => 'page', 'slug' => 'about'));

    // Users
    Router::connect('/user/:username', array('controller' => 'users', 'action' => 'view'), array('pass' => array('username')));

    // Contact
    Router::connect('/contact', array('controller' => 'contacts', 'action' => 'view', 'contact'));
?>