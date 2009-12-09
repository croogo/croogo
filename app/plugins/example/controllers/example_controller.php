<?php
/**
 * Example Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleController extends ExampleAppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Example';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Setting');

    function index() {
        $this->pageTitle = __('Example', true);

        $this->set('exampleVariable', 'value here');
    }

}
?>