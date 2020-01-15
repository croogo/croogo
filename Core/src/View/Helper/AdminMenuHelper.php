<?php

namespace Croogo\Core\View\Helper;

use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

class AdminMenuHelper extends Helper
{
    use StringTemplateTrait;

    public $helpers = [
        'Html',
        'Url',
    ];

    protected $_defaultConfig = [
        'templates' => [
            'list' => '<ul{{attrs}}>{{content}}</ul>',
            'listItem' => '<li{{attrs}}>{{content}}</li>',
            'listItemLink' => '<a{{attrs}}>{{icon}}{{content}}{{caret}}</a>{{children}}',
            'listItemIcon' => '<i{{attrs}}></i>',

        ],

        'attributes' => [
            'listAttrs' => [
                'class' => 'nav sidebar-inner',
                'id' => 'sidebar-menu',
            ],
            'listItemAttrs' => [
                'class' => 'sidenav-item-link',
            ],
        ],
    ];

    /**
     * Internal variable holding the current url being processed
     */
    private $menuUrl = null;

    public function render($items, $options = [])
    {
        $output = '';

        $options = Hash::merge($this->_defaultConfig['attributes'], $options);

        $listAttrs = $options['listAttrs'];

        $sorted = Hash::sort($items, '{s}.weight', 'ASC');

        foreach ($sorted as $key => $item) {
            $output .= $this->listItem($item, $options);
        }

        return $this->formatTemplate('list', [
            'content' => $output,
            'attrs' => $this->templater()->formatAttributes($listAttrs),
        ]);
    }

    protected function listItem($item, $options = [])
    {
        $this->menuUrl = $this->Url->build($item['url']);
        $options = [
            'content' => $this->listItemLink($item, $options),
        ];
        if ($this->menuUrl == env('REQUEST_URI')) {
            $options['attrs'] = $this->templater()->formatAttributes([
                'class' => 'active',
            ]);
        }

        return $this->formatTemplate('listItem', $options);
    }

    protected function listItemLink($item, $options = [])
    {
        $templater = $this->templater();
        $hasChildren = !empty($item['children']);
        $targetId = Inflector::slug(strtolower($item['title']));
        $listItemAttrs = $options['listItemAttrs'];
        $listItemAttrs['href'] = $this->menuUrl;

        if ($hasChildren) {
            $listItemAttrs['data-target'] = '#' . $targetId;
            $listItemAttrs['data-toggle'] = 'collapse';

            $childOptions = $options;
            $childOptions['submenuListAttrs'] = [
                'id' => $targetId,
                'class' => 'sub-menu collapse',
                'data-toggle' => 'collapse',
            ];
        }

        return $this->formatTemplate('listItemLink', [
            'attrs' => $templater->formatAttributes($listItemAttrs),
            'content' => '<span class="nav-text">' . $item['title'] . '</span>',
            'icon' => isset($item['icon'])
                ? $this->formatTemplate('listItemIcon', [
                    'attrs' => $templater->formatAttributes([
                        'class' => 'mdi mdi-' . $item['icon'],
                    ]),
                ])
                : null,
            'caret' => $hasChildren ? '<b class="caret"></b>' : null,
            'children' => $hasChildren ? $this->submenu($item['children'], $childOptions) : null
        ]);
    }

    protected function submenu($items, $options)
    {
        $output = '';
        foreach ($items as $key => $item) {
            $output .= $this->listItem($item, $options);
        }

        $submenuListAttrs = $options['submenuListAttrs'];
        return $this->formatTemplate('list', [
            'content' => $output,
            'attrs' => $this->templater()->formatAttributes($submenuListAttrs),
        ]);
    }

}
