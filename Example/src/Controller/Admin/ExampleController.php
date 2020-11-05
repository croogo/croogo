<?php
declare(strict_types=1);

namespace Croogo\Example\Controller\Admin;

/**
 * Example Controller
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class ExampleController extends AppController
{

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Example';

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = ['Setting'];

    /**
     * admin_index
     *
     * @return void
     */
    public function index()
    {
        $this->set('title_for_layout', 'Example');
    }

    /**
     * admin_chooser
     *
     * @return void
     */
    public function chooser()
    {
        $this->set('title_for_layout', 'Chooser Example');
    }

    public function add()
    {
    }

    public function rteExample()
    {
        $notice = 'If editors are not displayed correctly, check that `Ckeditor` plugin is loaded after `Example` plugin.';
        $this->Flash->success($notice);
    }
}
