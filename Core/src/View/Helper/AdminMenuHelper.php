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

            'submenuList' => '<ul{{attrs}}><div class="sub-menu">{{content}}</div></ul>',
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
        return $this->formatTemplate('listItem', [
            'content' => $this->listItemLink($item, $options),
        ]);
    }

    protected function listItemLink($item, $options = [])
    {
        $templater = $this->templater();
        $hasChildren = !empty($item['children']);
        $targetId = Inflector::slug(strtolower($item['title']));
        $listItemAttrs = $options['listItemAttrs'];
        $listItemAttrs['href'] = $this->Url->build($item['url']);
        if ($hasChildren) {
            $listItemAttrs['data-target'] = '#' . $targetId;
            $listItemAttrs['data-toggle'] = 'collapse';

            $childOptions = $options;
            $childOptions['submenuListAttrs'] = [
                'id' => $targetId,
                'class' => 'collapse',
                'data-toggle' => 'collapse',
            ];

        }

        return $this->formatTemplate('listItemLink', [
            'attrs' => $templater->formatAttributes($listItemAttrs),
            'content' => '<span class="nav-text">' . $item['title'] . '</span>',
            'icon' => isset($item['icon'])
                ? $this->formatTemplate('listItemIcon', [
                    'attrs' => $templater->formatAttributes([
                        'class' => 'fa fa-' . $item['icon'],
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
        return $this->formatTemplate('submenuList', [
            'content' => $output,
            'attrs' => $this->templater()->formatAttributes($submenuListAttrs),
        ]);
    }

}
