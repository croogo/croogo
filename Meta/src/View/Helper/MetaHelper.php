<?php

namespace Croogo\Meta\View\Helper;

use Cake\View\Helper;

/**
 * Meta Helper
 *
 * @category Meta.View/Helper
 * @package  Croogo.Meta
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaHelper extends Helper
{

/**
 * Helpers
 */
    public $helpers = [
        'Croogo/Core.Layout',
        'Croogo/Core.Croogo',
        'Html',
        'Form',
    ];

    public $settings = [
        'deleteUrl' => [
            'admin' => true, 'plugin' => 'meta',
            'controller' => 'meta', 'action' => 'delete_meta',
        ],
    ];

/**
 * beforeRender
 */
    public function beforeRender($viewFile)
    {
        if ($this->_View->Layout->isLoggedIn()) {
            return $this->Croogo->adminScript('Meta.admin');
        }
    }

/**
 * Meta tags
 *
 * @return string
 */
    public function meta($metaForLayout = [])
    {
        $_metaForLayout = [];
        if (is_array(Configure::read('Meta'))) {
            $_metaForLayout = Configure::read('Meta');
        }

        if (count($metaForLayout) == 0 &&
            isset($this->_View->viewVars['node']['CustomFields']) &&
            count($this->_View->viewVars['node']['CustomFields']) > 0) {
            $metaForLayout = [];
            foreach ($this->_View->viewVars['node']['CustomFields'] as $key => $value) {
                if (strstr($key, 'meta_')) {
                    $key = str_replace('meta_', '', $key);
                    $metaForLayout[$key] = $value;
                }
            }
        }

        $metaForLayout = array_merge($_metaForLayout, $metaForLayout);

        $output = '';
        foreach ($metaForLayout as $name => $content) {
            if (is_array($content) && isset($content['content'])) {
                $attr = key($content);
                $attrValue = $content[$attr];
                $value = $content['content'];
            } else {
                $attr = 'name';
                $attrValue = $name;
                $value = $content;
            }
            $output .= '<meta ' . $attr . '="' . $attrValue . '" content="' . $value . '" />';
        }

        return $output;
    }

/**
 * Meta field: with key/value fields
 *
 * @param string $key (optional) key
 * @param string $value (optional) value
 * @param int$id(optional) ID of Meta
 * @param array $options (optional) options
 * @return string
 */
    public function field($key = '', $value = null, $id = null, $options = [])
    {
        $inputClass = $this->Layout->cssClass('formInput');
        $_options = [
            'key' => [
                'label' => __d('croogo', 'Key'),
                'value' => $key,
            ],
            'value' => [
                'label' => __d('croogo', 'Value'),
                'value' => $value,
                'type' => 'textarea',
                'rows' => 2,
            ],
        ];
        if ($inputClass) {
            $_options['key']['class'] = $_options['value']['class'] = $inputClass;
        }
        $options = Hash::merge($_options, $options);
        $uuid = String::uuid();

        $fields = '';
        if ($id != null) {
            $fields .= $this->Form->input('Meta.' . $uuid . '.id', ['type' => 'hidden', 'value' => $id]);
            $this->Form->unlockField('Meta.' . $uuid . '.id');
        }
        $fields .= $this->Form->input('Meta.' . $uuid . '.key', $options['key']);
        $fields .= $this->Form->input('Meta.' . $uuid . '.value', $options['value']);
        $this->Form->unlockField('Meta.' . $uuid . '.key');
        $this->Form->unlockField('Meta.' . $uuid . '.value');
        $fields = $this->Html->tag('div', $fields, ['class' => 'fields']);

        $id = is_null($id) ? $uuid : $id;
        $deleteUrl = $this->settings['deleteUrl'];
        $deleteUrl[] = $id;
        $actions = $this->Html->link(
            __d('croogo', 'Remove'),
            $deleteUrl,
            ['class' => 'remove-meta', 'rel' => $id]
        );
        $actions = $this->Html->tag('div', $actions, ['class' => 'actions']);

        $output = $this->Html->tag('div', $actions . $fields, ['class' => 'meta']);
        return $output;
    }
}
