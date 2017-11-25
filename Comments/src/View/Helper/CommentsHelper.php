<?php

namespace Croogo\Comments\View\Helper;

use Cake\Utility\Inflector;
use Cake\View\Helper;
use Croogo\Core\Croogo;

/**
 * Comments Helper
 *
 * @category Comments.View/Helper
 * @package  Croogo.Comments.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsHelper extends Helper
{

    /**
     * beforeRender
     */
    public function beforeRender($viewFile)
    {
        if ($this->request->param('prefix') === 'admin' && !$this->request->is('ajax')) {
            $this->_adminTabs();
        }
    }

    /**
     * Hook admin tabs when type allows commenting
     */
    protected function _adminTabs()
    {
        $controller = $this->request->param('controller');
        if ($controller === 'Types' || empty($this->_View->viewVars['type']->comment_status)) {
            return;
        }
        $title = __d('croogo', 'Comments');
        $element = 'Croogo/Comments.comments_tab';
        Croogo::hookAdminTab('Admin/' . $controller . '/add', $title, $element);
        Croogo::hookAdminTab('Admin/' . $controller . '/edit', $title, $element);
    }
}
