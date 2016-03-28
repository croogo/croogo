<?php

namespace Croogo\Core\View\Helper;

use Cake\Event\Event;
use Cake\Utility\Hash;
use BootstrapUI\View\Helper\HtmlHelper;
use Cake\View\View;
use Croogo\Core\Status;
use Croogo\Extensions\CroogoTheme;

/**
 * Croogo Html Helper
 *
 * @package Croogo.Croogo.View.Helper
 */
class CroogoHtmlHelper extends HtmlHelper
{

    public $helpers = [
        'Url',
        'Croogo/Core.Theme',
    ];

/**
 * Constructor
 */
    public function __construct(View $View, $settings = [])
    {
        if ($View->theme) {
            $themeConfig = CroogoTheme::config($View->theme);
            $themeSettings = $themeConfig['settings'];
            $settings = Hash::merge($themeSettings, $settings);
        }

        parent::__construct($View, $settings);

        if (!$View->theme) {
            return;
        }

        $themeCss = $themeSettings['css'];
        $boxIconClass = '';

        $this->_defaultConfig['templates']['beginbox'] =
            "<div class='$themeCss[row]'>
				<div class='$themeCss[columnFull]'>
					<div class='box'>
						<div class='box-title'>
							<i class='$boxIconClass'></i>
							%s
						</div>
						<div class='box-content %s'>";
        $this->_defaultConfig['templates']['endbox'] =
                        '</div>
					</div>
				</div>
			</div>';
        $this->_defaultConfig['templates']['icon'] = '<i class="%s"%s></i>';
    }

/**
 * Creates a formatted IMG element.
 *
 * @see HtmlHelper::image()
 * @param string $path Image Path
 * @param array $options Options list
 * @return string Completed img tag
 */
    public function image($path, array $options = [])
    {
        $class = $this->Theme->getCssClass('imageClass');
        if (empty($options['class'])) {
            $options['class'] = $class;
        }
        return parent::image($path, $options);
    }

/**
 * Creates a formatted IMG element for preview images.
 *
 * @see HtmlHelper::image()
 * @param string $path Image Path
 * @param array $options Options list
 * @return string Completed img tag
 */
    public function thumbnail($path, $options = [])
    {
        $class = $this->Theme->getCssClass('thumbnailClass');
        if (empty($options['class'])) {
            $options['class'] = $class;
        }
        return parent::image($path, $options);
    }

/**
 * Create a string representing the start of a box container
 *
 * @param string $title Box title
 * @param bool$isHiddenWhen true, container will have 'hidden' class
 * @param bool$isLabelHiddenWhen true, container will have 'label-hidden' class
 * @returns string Start of box markup
 */
    public function beginBox($title, $isHidden = false, $isLabelHidden = false)
    {
        $isHidden = $isHidden ? 'hidden' : '';
        $isLabelHidden = $isLabelHidden ? 'label-hidden' : '';
        $class = $isHidden . ' ' . $isLabelHidden;

        $output = '
			<div class="row-fluid">
				<div class="span12">
					<div class="box">';

        $output .= $this->div('box-title', '<i class="icon-list"></i> ' . $title, ['escape' => false]);

        $output .= '<div class="box-content ' . $class . '">';

        return $output;
    }

/**
 * Create a string that ends a box container
 *
 * @return string Box end markup
 */
    public function endBox()
    {
        return '		</div>
					</div>
				</div>
			</div>';
    }

/**
 * Returns a icon markup
 *
 * @param string $name Icon name/identifier without the prefix
 * @param array $options Icon html attributes
 * @return string Icon markup
 */
    public function icon($name, array $options = [])
    {
        $iconDefaults = $this->config('iconDefaults');

        $defaults = [
            'iconSet' => $iconDefaults['iconSet'],
        ];
        $options += $defaults;
        return parent::icon($this->Theme->getIcon($name), $options);
    }

/**
 * Create a link with icons with XHR toggleable status values
 *
 * @param string $value Current value
 * @param array $url Url in array format
 */
    public function status($value, $url = [])
    {
        $icon = $value == Status::PUBLISHED ? $this->Theme->getIcon('check-mark') : $this->Theme->getIcon('x-mark');
        $class = $value == Status::PUBLISHED ? 'green' : 'red';
        $iconTag = $this->icon($icon, ['class' => $class]);

        if (empty($url)) {
            return $iconTag;
        } else {
            return $this->link($iconTag, 'javascript:void(0);', [
                'escape' => false,
                'data-url' => $this->Url->build($url),
                'class' => "$class ajax-toggle"
            ]);
        }
    }

/**
 * Add possibilities to parent::link() method
 *
 * ### Options
 *
 * - `escape` Set to true to enable escaping of title and attributes.
 * - `button` 'primary', 'info', 'success', 'warning', 'danger', 'inverse', 'link'. http://twitter.github.com/bootstrap/base-css.html#buttons
 * - `icon` 'ok', 'remove' ... http://fortawesome.github.com/Font-Awesome/
 *
 * @param string $title The content to be wrapped by <a> tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array $options Array of HTML attributes.
 * @param string $confirmMessage JavaScript confirmation message.
 * @return string An `<a />` element.
 */
    public function link($title, $url = null, array $options = [], $confirmMessage = false)
    {
        $defaults = ['escape' => false];
        $options = is_null($options) ? [] : $options;
        $options = array_merge($defaults, $options);
        $iconDefaults = $this->config('iconDefaults');

        if (!empty($options['button'])) {
            if (!empty($options['class'])) {
                $class = $options['class'];
            }
            $buttons = ['btn'];
            foreach ((array)$options['button'] as $button) {
                $buttons[] = 'btn-' . $button;
            }
            $options['class'] = trim(join(' ', $buttons));
            $options['class'] .= isset($class) ? ' ' . $class : null;
            unset($options['button']);
        }

        if (isset($options['icon'])) {
            $iconSize = $iconDefaults['largeIconClass'];
            if (isset($options['iconSize']) && $options['iconSize'] === 'small') {
                $iconSize = $iconDefaults['smallIconClass'];
                unset($options['iconSize']);
            }
            if (empty($options['iconInline'])) {
                $title = $this->icon($options['icon'], ['class' => $iconSize]) . $title;
            } else {
                $icon = trim($iconSize . ' ' . $iconDefaults['classPrefix'] . $this->Theme->getIcon($options['icon']));
                if (isset($options['class'])) {
                    $options['class'] .= ' ' . $icon;
                } else {
                    $options['class'] = ' ' . $icon;
                }
                unset($options['iconInline']);
            }
            unset($options['icon']);
        }

        if (isset($options['tooltip'])) {
            $tooltipOptions = [
                'rel' => 'tooltip',
                'data-placement' => 'top',
                'data-trigger' => 'hover',
            ];
            if (is_string($options['tooltip'])) {
                $tooltipOptions = array_merge([
                    'data-title' => $options['tooltip'],
                ], $tooltipOptions);
                $options = array_merge($options, $tooltipOptions);
            } else {
                $options['tooltip'] = array_merge($tooltipOptions, $options['tooltip']);
                $options = array_merge($options, $options['tooltip']);
            }
            unset($options['tooltip']);
        }

        return parent::link($title, $url, $options, $confirmMessage);
    }

/**
 * @deprecated Use FileManagerHelper::breadcrumb()
 */
    public function addPath($path, $separator)
    {
        $path = explode($separator, $path);
        $currentPath = '';
        foreach ($path as $p) {
            if (!is_null($p)) {
                $currentPath .= $p . $separator;
                $this->addCrumb($p, $currentPath);
            }
        }
        return $this;
    }

    public function addCrumb($name, $link = null, array $options = [])
    {
        parent::addCrumb($name, $link, $options);
        return $this;
    }

/**
 * Checks that crumbs has been added/initialied
 *
 * @return boolean True if crumbs has been populated
 */
    public function hasCrumbs()
    {
        return !empty($this->_crumbs);
    }

/**
 * Starts a new tab pane
 *
 * @param string $id Tab pane id
 * @param array $options Options array
 * @return string
 */
    public function tabStart($id, $options = [])
    {
        $options = Hash::merge([
            'id' => $id,
            'class' => 'tab-pane',
        ], $options);
        return $this->formatTemplate('blockstart', [
            'attrs' => $this->templater()->formatAttributes($options)
        ]);
    }

/**
 * Ends a tab pane
 *
 * @return string
 */
    public function tabEnd()
    {
        return $this->formatTemplate('blockend', []);
    }
}
