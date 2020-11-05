<?php
declare(strict_types=1);

namespace Croogo\Wysiwyg\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use Croogo\Core\Routing\Router;

/**
 * Wysiwyg Helper
 *
 * @category Wysiwyg.Helper
 * @package  Croogo.Wysiwyg.View.Helper
 * @version  1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Croogo\Core\View\Helper\CroogoHelper $Croogo
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class WysiwygHelper extends Helper
{

    /**
     * Other helpers used by this helper
     *
     * @var array
     * @access public
     */
    public $helpers = [
        'Croogo',
        'Url'
    ];

    /**
     * beforeRender
     *
     * @param string $viewFile
     * @return void
     */
    public function beforeRender($viewFile)
    {
        $actions = array_keys(Configure::read('Wysiwyg.actions'));
        $currentAction = Router::getActionPath($this->getView()->getRequest(), true);
        $included = in_array($currentAction, $actions);
        if ($included) {
            $this->Croogo->adminScript('Croogo/Wysiwyg.wysiwyg.js');
        }
    }
}
