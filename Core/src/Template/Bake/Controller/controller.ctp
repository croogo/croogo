<%
/**
 * Controller bake template file
 *
 * Allows templating of Controllers generated from bake.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Utility\Inflector;

$defaultModel = $name;
%>
<?php

namespace <%= $namespace %>\Controller<%= $prefix %>;

<% if (class_exists("$namespace\Controller\$prefix\AppController")): %>
use <%= $namespace %>\Controller<%= $prefix %>\AppController as CroogoController;
<% else: %>
use Croogo\Core\Controller<%= $prefix %>\AppController as CroogoController;
<% endif; %>

/**
 * <%= $name %> Controller
 *
 * @property \<%= $namespace %>\Model\Table\<%= $defaultModel %>Table $<%= $defaultModel %>
<%
foreach ($components as $component):
    $classInfo = $this->Bake->classInfo($component, 'Controller/Component', 'Component');
%>
 * @property <%= $classInfo['fqn'] %> $<%= $classInfo['name'] %>
<% endforeach; %>
 */
class <%= $name %>Controller extends CroogoController
{
<%
echo $this->Bake->arrayProperty('helpers', $helpers, ['indent' => false]);
echo $this->Bake->arrayProperty('components', $components, ['indent' => false]);
foreach($actions as $action) {
    echo $this->element('Controller/' . $action);
}
%>

}
