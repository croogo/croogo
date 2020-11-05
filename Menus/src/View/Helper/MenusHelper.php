<?php
declare(strict_types=1);

namespace Croogo\Menus\View\Helper;

use Cake\Event\Event;
use Cake\Log\LogTrait;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\View;
use Croogo\Core\Nav;
use Croogo\Core\Utility\StringConverter;

/**
 * Menus Helper
 *
 * @category Menus.View/Helper
 * @package  Croogo.Menus.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Croogo\Core\View\Helper\LayoutHelper $Layout
 */
class MenusHelper extends Helper
{

    use LogTrait;

    public $helpers = [
        'Html',
        'Layout',
    ];

    /**
     * constructor
     */
    public function __construct(View $view, $settings = [])
    {
        parent::__construct($view, $settings);
        $this->_setupEvents();
        $this->_converter = new StringConverter();
    }

    /**
     * setup events
     */
    protected function _setupEvents()
    {
        $events = [
            'Helper.Layout.beforeFilter' => [
                'callable' => 'filter', 'passParams' => true,
            ],
        ];
        $eventManager = $this->_View->getEventManager();
        foreach ($events as $name => $config) {
            $eventManager->on($name, $config, [$this, 'filter']);
        }
    }

    /**
     * beforeRender
     */
    public function beforeRender($viewFile)
    {
        $request = $this->getView()->getRequest();
        if (($request->getParam('prefix') === 'Admin') && (!$request->is('ajax'))) {
            $this->_adminMenu();
            if ($request->getParam('plugin') == 'Croogo/Menus') {
                $this->_View->Js->buffer('Links.init();');
            }
        }
    }

    /**
     * Inject admin menu items
     */
    protected function _adminMenu()
    {
        $menus = $this->_View->get('menus_for_admin_layout');
        if (empty($menus)) {
            return;
        }
        if (!Nav::check('sidebar', 'menus')) {
            return;
        }
        foreach ($menus as $menu) {
            $weight = 9999 + $menu->weight;
            $htmlAttributes = $this->__isCurrentMenu($menu->id) ? ['class' => 'current'] : [];
            Nav::add('sidebar', 'menus.children.' . $menu->alias, [
                'title' => $menu->title,
                'url' => [
                    'prefix' => 'Admin',
                    'plugin' => 'Croogo/Menus',
                    'controller' => 'Links',
                    'action' => 'index',
                    '?' => ['menu_id' => $menu->id]
                ],
                'weight' => $weight,
                'htmlAttributes' => $htmlAttributes
            ]);
        };
    }

    /**
     * Checks wether $id is the current active menu
     *
     * The value is checked against the menuId variable set in
     * LinksController::admin_add() and LinksController::admin_edit()
     *
     * @param string $id Menu id
     * @return bool True if $id is currently the active menu
     */
    private function __isCurrentMenu($id)
    {
        $currentMenuId = $this->_View->get('menuId');

        return $currentMenuId === $id;
    }

    /**
     * Filter content for Menus
     *
     * Replaces [menu:menu_alias] or [m:menu_alias] with Menu list
     *
     * @param Event $event
     * @return string
     */
    public function filter(Event $event, $options = [])
    {
        $data = $event->getData();
        preg_match_all('/\[(menu|m):([A-Za-z0-9_\-]*)(.*?)\]/i', $data['content'], $tagMatches);
        for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $menuAlias = $tagMatches[2][$i];
            $options = [];
            for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $options = Hash::expand($options);
            $data['content'] = str_replace($tagMatches[0][$i], $this->verticalNav($menuAlias, $options), $data['content']);
        }

        return $event->getData();
    }

    /**
     * Output simple vertical nav
     */
    protected function verticalNav($menuAlias, $options = [])
    {
        $menusForLayout = $this->_View->get('menusForLayout');
        $menu = Hash::get($menusForLayout, "$menuAlias");
        if (!$menu) {
            return false;
        }
        $items = [];
        $roleId = $this->Layout->getRoleId();
        foreach ($menu['threaded'] as $item) {
            if (!empty($item->visibility_roles) && !in_array($roleId, $item->visibility_roles)) {
                continue;
            }

            $url = $item->link->getUrl();
            try {
                $items[] = $this->Html->link($item->title, $url, [
                    'class' => 'nav-link',
                ]);
            } catch (MissingRouteException $e) {
                $this->log('Cannot normalize url: ' . print_r($url, true), LOG_WARNING);
            }
        }
        if (!$items) {
            return null;
        }

        return $this->Html->tag('nav', implode('', $items), [
            'class' => 'nav flex-column',
        ]);
    }

    /**
     * Show Menu by Alias
     *
     * @param string $menuAlias Menu alias
     * @param array $options (optional)
     * @return string
     */
    public function menu($menuAlias, $options = [])
    {
        $_options = [
            'tag' => 'ul',
            'tagAttributes' => [
                'class' => 'dropdown-menu bg-dark',
            ],
            'subTag' => 'li',
            'subTagAttributes' => [
                'class' => 'nav-item',
            ],
            'linkAttributes' => [
                'class' => 'nav-link js-scroll-trigger',
            ],
            'selected' => 'selected',
            'dropdown' => false,
            'dropdownClass' => 'navbar-nav ml-auto',
            'element' => 'Croogo/Menus.menu',
        ];
        $options = array_merge($_options, $options);

        $menusForLayout = $this->_View->get('menusForLayout');
        if (!isset($menusForLayout[$menuAlias])) {
            return false;
        }
        $menu = $menusForLayout[$menuAlias];
        $output = $this->_View->element($options['element'], [
            'menu' => $menu,
            'options' => $options,
        ]);

        return $output;
    }

    /**
     * Merge Link options retrieved from Params behavior
     *
     * @param array $link Link data
     * @param string $param Parameter name
     * @param array $options Default options
     * @return array
     */
    protected function _mergeLinkParams($link, $param, $options = [])
    {
        if (isset($link['Params'][$param])) {
            $options = array_merge($options, $link['Params'][$param]);
        }

        $booleans = ['true', 'false'];
        foreach ($options as $key => $val) {
            if ($val == null) {
                unset($options[$key]);
            }
            if (is_string($val) && in_array(strtolower($val), $booleans)) {
                $options[$key] = ($val === 'true');
            }
        }

        return $options;
    }

    /**
     * Nested Links
     *
     * @param array $links model output (threaded)
     * @param array $options (optional)
     * @param int $depth level
     * @return string
     */
    public function nestedLinks($links, $options = [], $depth = 1)
    {
        $_options = [
            'linkAttributes' => []
        ];
        $options = array_merge($_options, $options);

        $roleId = $this->Layout->getRoleId();
        $output = '';
        foreach ($links as $link) {
            $linkAttr = $options['linkAttributes'] + [
                'id' => 'link-' . $link->id,
                'rel' => $link->rel,
                'target' => $link->target,
                'title' => $link->description,
                'class' => $link->class,
            ];

            if (!empty($link->visibility_roles) && !in_array($roleId, $link->visibility_roles)) {
                continue;
            }

            $linkAttr = $this->_mergeLinkParams($link, 'liAttr', $linkAttr);

            if (!empty($link->class) && strpos($linkAttr['class'], $link->class)) {
                $linkAttr['class'] = $this->addClass($linkAttr['class'], $link->class);
            }

            // Remove locale part before comparing links
            if ($this->getView()->getRequest()->getParam('locale')) {
                $currentUrl = substr($this->getView()->getRequest()->getPath(), strlen($this->getView()->getRequest()->getParam('locale') . '/'));
            } else {
                $currentUrl = $this->getView()->getRequest()->getPath();
            }

            try {
                if (Router::url($link->link->getUrl()) == Router::url('/' . $currentUrl)) {
                    if (!isset($linkAttr['class'])) {
                        $linkAttr['class'] = '';
                    }
                    $linkAttr['class'] .= ' ' . $options['selected'];
                }
            } catch (MissingRouteException $e) {
                $this->log(
                    sprintf(
                        'MissingRouteException for menu id %d - %s:',
                        $link->id,
                        $link->title
                    ),
                    LOG_WARNING
                );
                $this->log($e->getMessage(), LOG_WARNING);
                continue;
            }

            if (isset($link['children']) && count($link['children']) > 0) {
                $linkAttr['class'] .= ' dropdown-toggle';
                $linkAttr['data-toggle'] = 'dropdown';
                $linkAttr['aria-haspopup'] = 'true';
                $linkAttr['aria-expanded'] = 'false';
            }

            $linkOutput = $this->Html->link($link->title, $link->link->getUrl(), $linkAttr);
            if (isset($link['children']) && count($link['children']) > 0) {
                $childOptions = $options;
                $childOptions['subTagAttributes']['class'] = 'dropdown-item bg-dark';
                $linkOutput .= $this->nestedLinks($link['children'], $childOptions, $depth + 1);
            }
            $liAttr = $this->_mergeLinkParams($link, 'liAttr');
            $liAttr = !empty($liAttr) ? $liAttr : $options['subTagAttributes'];

            if (isset($link['children']) && count($link['children']) > 0) {
                $liAttr['class'] .= ' dropdown';
            }

            $linkOutput = $this->Html->tag($options['subTag'], $linkOutput, $liAttr);
            $output .= $linkOutput;
        }
        if ($output != null) {
            $tagAttr = $options['tagAttributes'];
            if ($options['dropdown'] && $depth == 1) {
                $tagAttr['class'] = $options['dropdownClass'];
            }
            $output = $this->Html->tag($options['tag'], $output, $tagAttr);
        }

        return $output;
    }

    /**
     * Converts strings like controller:abc/action:xyz/ to arrays
     *
     * @param string|array $link link
     * @return array
     * @see Use StringConverter::linkStringToArray()
     */
    public function linkStringToArray($link)
    {
        return $this->_converter->linkStringToArray($link);
    }

    /**
     * Converts array into string controller:abc/action:xyz/value1/value2
     *
     * @param array $url link
     * @return array
     * @see StringConverter::urlToLinkString()
     */
    public function urlToLinkString($url)
    {
        return $this->_converter->urlToLinkString($url);
    }
}
