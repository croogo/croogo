<?php
declare(strict_types=1);

namespace Croogo\Core\View\Helper;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\View;
use Croogo\Core\Database\Type\ParamsType;
use Croogo\Core\PluginManager;
use Croogo\Core\Status;

/**
 * Croogo Helper
 *
 * @category Helper
 * @package  Croogo.Croogo.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Croogo\Core\View\Helper\LayoutHelper $Layout
 * @property \Croogo\Core\View\Helper\ThemeHelper $Theme
 * @property \Croogo\Menus\View\Helper\MenusHelper $Menus
 * @property \Croogo\Acl\View\Helper\AclHelper $Acl
 */
class CroogoHelper extends Helper
{

    /**
     * @var array
     */
    public $helpers = [
        'Form',
        'Html' => [
            'className' => 'Croogo/Core.Html'
        ],
        'Url',
        'Croogo/Core.Layout',
        'Croogo/Core.Theme',
        'Croogo/Menus.Menus',
        'Croogo/Acl.Acl',
    ];

    /**
     * ParamsType instance
     *
     * @var Croogo\Core\Database\Type\ParamsType;
     */
    protected $_ParamsType;

    /**
     * Default Constructor
     *
     * @param View $View The View this helper is being attached to.
     * @param array $settings Configuration settings for the helper.
     */
    public function __construct(View $View, $settings = [])
    {
        $this->helpers[] = 'Croogo/Acl.Acl';
        parent::__construct($View, $settings);
        $this->_CroogoStatus = new Status();
        $this->_ParamsType = new ParamsType;
    }

    /**
     * @return array
     */
    public function statuses()
    {
        return $this->_CroogoStatus->statuses();
    }

    /**
     * Convenience method to Html::script() for admin views
     *
     * This method does nothing if request is ajax or not in admin prefix.
     *
     * @param string|array  $url Javascript files to include
     * @param array|bool $options Options or Html attributes
     * @return string|null String of <script /> tags or null
     * @see HtmlHelper::script()
     */
    public function adminScript($url, $options = [])
    {
        $options = Hash::merge(['block' => true, 'defer' => true], $options);
        $request = $this->getView()->getRequest();
        if ($request->is('ajax') || $request->getParam('prefix') !== 'Admin') {
            return null;
        }

        return $this->Html->script($url, $options);
    }

    /** Generate Admin menus added by Nav::add()
     *
     * @param array $menus
     * @param array $options
     * @return string menu html tags
     */
    public function adminMenus($menus, $options = [], $depth = 0)
    {
        $options = Hash::merge([
            'type' => 'sidebar',
            'children' => true,
            'htmlAttributes' => [
                'class' => 'nav flex-column',
            ],
            'itemTag' => 'li',
            'listTag' => 'ul',
        ], $options);

        $userId = $this->getView()->getRequest()->getSession()->read('Auth.User.id');
        if (empty($userId)) {
            return '';
        }

        $sidebar = $options['type'] === 'sidebar';
        $htmlAttributes = $options['htmlAttributes'];
        $out = null;
        $sorted = Hash::sort($menus, '{s}.weight', 'ASC');
        if (empty($this->Role)) {
            $this->Role = TableRegistry::getTableLocator()->get('Croogo/Users.Roles');
            $this->Role->addBehavior('Croogo/Core.Aliasable');
        }
        $currentRole = $this->Role->byId($this->Layout->getRoleId());

        foreach ($sorted as $menu) {
            if (isset($menu['separator'])) {
                if ($options['itemTag'] === false) {
                    $liOptions['class'] = 'dropdown-divider';
                    $out .= $this->Html->tag('div', '', $liOptions);
                } else {
                    $liOptions['class'] = 'divider';
                    $out .= $this->Html->tag($options['itemTag'], '', $liOptions);
                }
                continue;
            }
            if ($currentRole != 'superadmin' && !$this->Acl->linkIsAllowedByUserId($userId, $menu['url'])) {
                continue;
            }

            if (empty($menu['htmlAttributes']['class'])) {
                $menuClass = Text::slug(strtolower('menu-' . $menu['title']), '-');
                $menu['htmlAttributes'] = Hash::merge([
                    'class' => $menuClass,
                ], $menu['htmlAttributes']);
            }
            $title = '';
            if ($menu['icon'] === false) {
            } elseif (empty($menu['icon'])) {
                $menu['htmlAttributes'] += ['icon' => 'white'];
            } else {
                $menu['htmlAttributes'] += ['icon' => $menu['icon']];
            }
            if ($sidebar) {
                $title .= '<span>' . h($menu['title']) . '</span>';
            } else {
                $title .= $menu['title'];
            }
            $children = '';
            if (!empty($menu['children'])) {
                $childClass = '';
                if ($sidebar) {
                    $itemTag = 'li';
                    $listTag = 'ul';
                    $childClass = 'nav flex-column sub-nav ';
                    $childClass .= ' submenu-' . Text::slug(strtolower($menu['title']), '-');
                    if ($depth > 0) {
                        $childClass .= ' dropdown-menu';
                    }
                } else {
                    if ($depth == 0) {
                        $childClass = 'dropdown-menu';
                    }
                    $itemTag = false;
                    $listTag = 'div';
                }
                $children = $this->adminMenus($menu['children'], [
                    'type' => $options['type'],
                    'children' => true,
                    'htmlAttributes' => ['class' => $childClass],
                    'itemTag' => $itemTag,
                    'listTag' => $listTag,
                ], $depth + 1);

                $menu['htmlAttributes']['class'] .= ' hasChild dropdown-close';
            }

            $menuUrl = $this->Url->build($menu['url']);
            if ($menuUrl == env('REQUEST_URI')) {
                if (isset($menu['htmlAttributes']['class'])) {
                    $menu['htmlAttributes']['class'] .= ' current';
                } else {
                    $menu['htmlAttributes']['class'] = 'current';
                }
            }

            if (!$sidebar && !empty($children)) {
                $menu['htmlAttributes']['class'] = 'dropdown-toggle';
                $menu['htmlAttributes']['data-toggle'] = 'dropdown';
            }

            if (!$sidebar && $depth == 0) {
                $menu['htmlAttributes']['class'] .= ' nav-link';
            } elseif (!$sidebar && $depth > 0) {
                $menu['htmlAttributes']['class'] .= ' dropdown-item';
            } else {
                $menu['htmlAttributes']['class'] .= ' sidebar-item';
            }

            if (isset($menu['before'])) {
                $title = $menu['before'] . $title;
            }

            if (isset($menu['after'])) {
                $title = $title . $menu['after'];
            }

            $menu['htmlAttributes']['escape'] = false;

            $link = $this->Html->link($title, $menu['url'], $menu['htmlAttributes']);
            if ($options['itemTag'] === false) {
                $out .= $link;
                continue;
            }

            $liOptions = [
                'class' => 'nav-item',
            ];
            if ($sidebar && !empty($children) && $depth > 0) {
                $liOptions['class'] .= ' dropdown-submenu';
            }
            if (!$sidebar && !empty($children)) {
                if ($depth > 0) {
                    $liOptions['class'] .= ' dropdown-submenu';
                } else {
                    $liOptions['class'] .= ' dropdown';
                }
            }

            $out .= $this->Html->tag($options['itemTag'], $link . $children, $liOptions);
        }

        if (!$sidebar && $depth > 0) {
            $htmlAttributes['class'] = 'dropdown-menu';
        }

        return $this->Html->tag($options['listTag'], $out, $htmlAttributes);
    }

    /**
     * Show links under Actions column
     *
     * @param int $id
     * @param array $options
     * @return string
     */
    public function adminRowActions($id, $options = [])
    {
        $request = $this->getView()->getRequest();
        $key = $output = '';
        $plugin = $request->getParam('plugin');
        if ($plugin) {
            $key .= $plugin . '.';
        }
        $prefix = $request->getParam('prefix');
        if ($prefix) {
            $key .= Inflector::camelize($prefix) . '/';
        }
        $key .= Inflector::camelize($this->getView()->getRequest()->getParam('controller')) . '/';
        $key .= $request->getParam('action');
        $encodedKey = base64_encode($key);
        $rowActions = Configure::read('Admin.rowActions.' . $encodedKey);
        if (is_array($rowActions)) {
            foreach ($rowActions as $title => $link) {
                $linkOptions = $options;
                if (is_array($link)) {
                    $config = $link[key($link)];
                    if (isset($config['options'])) {
                        $linkOptions = Hash::merge($options, $config['options']);
                    }
                    if (isset($config['confirm'])) {
                        $linkOptions['confirm'] = $config['confirm'];
                        unset($config['confirm']);
                    }
                    if (isset($config['title'])) {
                        $title = $config['title'];
                    }
                    $link = key($link);
                }
                $link = $this->Menus->linkStringToArray(str_replace(':id', $id, $link));
                if (isset($linkOptions['icon'])) {
                    $linkOptions['escapeTitle'] = false;
                }
                $output .= $this->adminRowAction($title, $link, $linkOptions);
            }
        }

        return $output;
    }

    /**
     * Show link under Actions column
     *
     * ### Options:
     *
     * - `method` - when 'POST' is specified, the FormHelper::postLink() will be
     *              used instead of HtmlHelper::link()
     * - `rowAction` when bulk submissions is used, defines which action to use.
     *
     * @param string $title The content to be wrapped by <a> tags.
     * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
     * @param array $options Array of HTML attributes.
     * @param string $confirmMessage JavaScript confirmation message.
     * @return string An `<a />` element
     */
    public function adminRowAction($title, $url = null, $options = [], $confirmMessage = false)
    {
        $action = false;
        $options = Hash::merge([
            'escapeTitle' => true,
            'escape' => true,
            'confirm' => $confirmMessage,
        ], $options);

        if (is_array($url)) {
            $action = $url['action'];
            if (isset($options['class'])) {
                $options['class'] .= ' ' . $url['action'];
            } else {
                $options['class'] = $url['action'];
            }
        }
        if (isset($options['icon']) && empty($title)) {
            $options['iconInline'] = false;
        }

        if (!empty($options['rowAction'])) {
            $options['data-row-action'] = $options['rowAction'];
            unset($options['rowAction']);

            return $this->_bulkRowAction($title, $url, $options);
        }

        if (!empty($options['method']) && strcasecmp($options['method'], 'post') == 0) {
            $usePost = true;
            unset($options['method']);
        }

        if ($action == 'delete' || isset($usePost)) {
            $options['block'] = true;
            $postLink = $this->Form->postLink($title, $url, $options);

            return $postLink;
        }

        return $this->Html->link($title, $url, $options);
    }

    /**
     * Creates a special type of link for use in admin area.
     *
     * Clicking the link will automatically check a corresponding checkbox
     * where element id is equal to $url parameter and immediately submit the form
     * it's on.  This works in tandem with Admin.processLink() in javascript.
     */
    protected function _bulkRowAction($title, $url = null, $options = [])
    {
        if (!empty($options['confirm'])) {
            $options['data-confirm-message'] = $options['confirm'];
            unset($options['confirm']);
        }
        if (isset($options['icon'])) {
            $options['iconInline'] = false;
        }
        $output = $this->Html->link($title, $url, $options);

        return $output;
    }

    /**
     * Create an action button
     *
     * @param string $title Button title
     * @param url|string $url URL
     * @param array $options Options array
     * @param string $confirmMessage Confirmation message
     * @return string
     */
    public function adminAction($title, $url, $options = [], $confirmMessage = false)
    {
        $options = Hash::merge([
            'button' => 'outline-secondary',
            'class' => 'btn-sm',
            'list' => false,
            'confirm' => $confirmMessage,
            'escape' => false,
        ], $options);
        if ($options['list'] === true) {
            $list = true;
            unset($options['list']);
        }
        if (isset($options['method']) && strcasecmp($options['method'], 'post') == 0) {
            $options['block'] = 'scriptBottom';
            $out = $this->Form->postLink($title, $url, $options);
        } else {
            $out = $this->Html->link($title, $url, $options);
        }
        if (isset($list)) {
            $out = $this->Html->tag('li', $out);
        } else {
            $out = $this->Html->div('btn-group', $out);
        }

        return $out;
    }

    /**
     * Create a tab title/link
     */
    public function adminTab($title, $url, $options = [])
    {
        $options = Hash::merge([
            'data-toggle' => 'tab',
        ], $options);

        $options = $this->addClass($options, 'nav-link');

        return $this->Html->tag('li', $this->Html->link($title, $url, $options), [
            'class' => 'nav-item',
        ]);
    }

    /**
     * Show tabs
     *
     * @return string
     */
    public function adminTabs($show = null)
    {
        if (!isset($this->adminTabs)) {
            $this->adminTabs = false;
        }

        $output = '';
        $actions = '';
        $request = $this->getView()->getRequest();
        if ($request->getParam('prefix')) {
            $actions .= Inflector::camelize($request->getParam('prefix')) . '/';
        }
        $actions .= Inflector::camelize($request->getParam('controller')) . '/' . $request->getParam('action');
        $tabs = Configure::read('Admin.tabs.' . $actions);
        if (is_array($tabs)) {
            foreach ($tabs as $title => $tab) {
                $tab = Hash::merge([
                    'options' => [
                        'linkOptions' => [],
                        'elementData' => [],
                        'elementOptions' => [],
                    ],
                ], $tab);

                $typeAlias = $this->getView()->get('typeAlias');
                $viewVar = $this->getView()->get('viewVar');

                if (!isset($tab['options']['type']) ||
                    (isset($tab['options']['type']) &&
                        (in_array($typeAlias, $tab['options']['type'])))
                ) {
                    $domId = strtolower(Inflector::singularize($request->getParam('controller'))) .
                        '-' .
                        strtolower(Text::slug($title, '-'));
                    if ($this->adminTabs) {
                        if (isset($viewVar)) {
                            $entity = $this->_View->get($viewVar);
                            $tab['options']['elementData']['entity'] = $entity;
                        }
                        $output .= $this->Html->tabStart($domId);
                        $output .= $this->_View->element(
                            $tab['element'],
                            $tab['options']['elementData'],
                            $tab['options']['elementOptions']
                        );
                        $output .= $this->Html->tabEnd();
                    } else {
                        $output .= $this->adminTab(__d('croogo', $title), '#' . $domId, $tab['options']['linkOptions']);
                    }
                }
            }
        }

        $this->adminTabs = true;

        return $output;
    }

    /**
     * Show Boxes
     *
     * @param array $boxName
     */
    public function adminBoxes($boxName = null)
    {
        if (!isset($this->boxAlreadyPrinted)) {
            $this->boxAlreadyPrinted = [];
        }

        $output = '';
        $request = $this->getView()->getRequest();
        $box = $request->getParam('controller') . '/' . $request->getParam('action');
        if ($request->getParam('prefix')) {
            $box = Inflector::camelize($request->getParam('prefix')) . '/' . $box;
        }
        $allBoxes = Configure::read('Admin.boxes.' . $box);
        $allBoxes = empty($allBoxes) ? [] : $allBoxes;
        $boxNames = [];

        if (is_null($boxName)) {
            foreach ($allBoxes as $boxName => $value) {
                if (!in_array($boxName, $this->boxAlreadyPrinted)) {
                    $boxNames[$boxName] = $allBoxes[$boxName];
                }
            }
        } elseif (!in_array($boxName, $this->boxAlreadyPrinted)) {
            if (array_key_exists($boxName, $allBoxes)) {
                $boxNames[$boxName] = $allBoxes[$boxName];
            }
        }

        foreach ($boxNames as $title => $box) {
            $box = Hash::merge([
                'options' => [
                    'linkOptions' => [],
                    'elementData' => [],
                    'elementOptions' => [],
                ],
            ], $box);
            $issetType = isset($box['options']['type']);
            $typeInTypeAlias = $issetType && in_array($this->_View->viewVars['typeAlias'], $box['options']['type']);
            if (!$issetType || $typeInTypeAlias) {
                if (isset($this->_View->viewVars['viewVar'])) {
                    $entity = $this->_View->viewVars[$this->_View->viewVars['viewVar']];
                    $box['options']['elementData']['entity'] = $entity;
                }
                $output .= $this->Html->beginBox($title);
                $output .= $this->_View->element(
                    $box['element'],
                    $box['options']['elementData'],
                    $box['options']['elementOptions']
                );
                $output .= $this->Html->endBox();
                $this->boxAlreadyPrinted[] = $title;
            }
        }

        return $output;
    }

    /**
     * @param $target string ID of target element
     */
    public function linkChooser(string $target): string
    {
        $linkChooser = $this->_View->element('Croogo/Core.admin/modal', [
            'id' => 'link-chooser',
            'modalSize' => 'modal-lg'
        ]);
        if (!strstr($this->_View->fetch('page-footer'), $linkChooser)) {
            $this->_View->append('page-footer', $linkChooser);
        }

        return $this->_View->cell('Croogo/Core.Admin/LinkChooser', [$target])->render();
    }

    /**
     * @param $theme
     * @param $path
     * @param null $allowedMimeTypes
     *
     * @return string|null
     */
    public function dataUri($theme, $path, $allowedMimeTypes = null)
    {
        $allowedMimeTypes = array_filter(array_merge([
            'image/jpeg',
            'image/png',
        ], (array)$allowedMimeTypes));
        if ($theme) {
            $file = PluginManager::path($theme) . '/webroot/' . $path;
        } else {
            $file = WWW_ROOT . $path;
        }
        if (!file_exists($file)) {
            return null;
        }
        $mimeType = mime_content_type($file);
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return null;
        }
        $dataUri = sprintf(
            'data:%s;base64,%s',
            $mimeType,
            base64_encode(file_get_contents($file))
        );

        return $dataUri;
    }
}
