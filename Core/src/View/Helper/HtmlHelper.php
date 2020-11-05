<?php
declare(strict_types=1);

namespace Croogo\Core\View\Helper;

use BootstrapUI\View\Helper\HtmlHelper as BaseHtmlHelper;
use Cake\Event\Event;
use Cake\Utility\Hash;
use Cake\View\View;
use Croogo\Core\Status;
use Croogo\Extensions\CroogoTheme;

/**
 * Croogo Html Helper
 *
 * @package Croogo.Croogo.View.Helper
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Croogo\Core\View\Helper\ThemeHelper $Theme
 */
class HtmlHelper extends BaseHtmlHelper
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
        if ($View->getTheme()) {
            $themeConfig = CroogoTheme::config($View->getTheme());
            $themeSettings = $themeConfig['settings'];
            $settings = Hash::merge($themeSettings, $settings);
        }
        $boxContainerClass = 'card';
        $boxHeaderClass = 'card-header';
        $boxBodyClass = 'card-body';
        if (isset($settings['css'])) {
            $boxContainerClass = $settings['css']['boxContainerClass'];
            $boxHeaderClass = $settings['css']['boxHeaderClass'];
            $boxBodyClass = $settings['css']['boxBodyClass'];
        }
        $this->_defaultConfig['templates']['beginbox'] = "<div class='$boxContainerClass'>
                        <div class='$boxHeaderClass'>
                            {{icon}} {{title}}
                        </div>
                        <div class='$boxBodyClass'>";
        $this->_defaultConfig['templates']['endbox'] = '</div>
            </div>';

        parent::__construct($View, $settings);

        if (!$View->getTheme()) {
            return;
        }
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        $events = parent::implementedEvents();
        $events['Helper.Layout.beforeFilter'] = [
            'callable' => 'filter',
            'passParams' => true,
        ];

        return $events;
    }

    /**
     * Filter content for Scripts and css tags
     *
     * Replaces [script:url] or [css:url] with script/css tags
     *
     * @param Event $event
     * @return string
     */
    public function filter(Event $event)
    {
        preg_match_all('/\[(script|css):([^] ]*)(.*?)\]/i', $event->getData('content'), $tagMatches);
        for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $options = [];
            for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $options = Hash::expand($options) + ['block' => true];

            if ($tagMatches[1][$i] === 'script') {
                $this->script($tagMatches[2][$i], $options);
            } elseif ($tagMatches[1][$i] === 'css') {
                $this->css($tagMatches[2][$i], $options);
            }

            $event->data['content'] = str_replace($tagMatches[0][$i], '', $event->data['content']);
        }

        return $event->getData();
    }

    /**
     * Creates a formatted IMG element.
     *
     * @see HtmlHelper::image()
     * @param string $path Image Path
     * @param array $options Options list
     * @return string Completed img tag
     */
    public function image($path, array $options = []): string
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
     * @param options $options Option array
     * @returns string Start of box markup
     */
    public function beginBox($title = '', array $options = [])
    {
        $options = Hash::merge([
            'icon' => 'list',
        ], $options);

        $icon = $this->icon($options['icon']);
        unset($options['icon']);

        return $this->formatTemplate('beginbox', [
            'attrs' => $this->templater()
                ->formatAttributes($options),
            'icon' => $icon,
            'title' => $title,
        ]);
    }

    /**
     * Create a string that ends a box container
     *
     * @return string Box end markup
     */
    public function endBox()
    {
        return $this->formatTemplate('endbox', []);
        ;
    }

    /**
     * Returns a icon markup
     *
     * @param string $name Icon name/identifier without the prefix
     * @param array $options Icon html attributes
     * @return string Icon markup
     */
    public function icon($name, array $options = []): string
    {
        $iconDefaults = $this->getConfig('iconDefaults');

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
                'class' => "$class ajax-toggle",
            ]);
        }
    }

    /**
     * Add possibilities to parent::link() method
     *
     * ### Options
     *
     * - `escape` Set to true to enable escaping of title and attributes.
     * - `button` 'primary', 'info', 'success', 'warning', 'danger', 'inverse', 'link'.
     * http://twitter.github.com/bootstrap/base-css.html#buttons
     * - `icon` 'ok', 'remove' ... http://fortawesome.github.com/Font-Awesome/
     *
     * @param string $title The content to be wrapped by <a> tags.
     * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
     * @param array $options Array of HTML attributes.
     * @return string An `<a />` element.
     */
    public function link($title, $url = null, array $options = []): string
    {
        $defaults = ['escape' => true];
        $options = is_null($options) ? [] : $options;
        $options = array_merge($defaults, $options);
        $iconDefaults = $this->getConfig('iconDefaults');

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
            $iconSize = $iconDefaults['size'];
            if (isset($options['iconSize']) && $options['iconSize'] === 'small') {
                $iconSize = $iconDefaults['size'];
                unset($options['iconSize']);
            }
            if (empty($options['iconInline'])) {
                $title = $this->icon($options['icon'], ['class' => $iconSize]) . $title;
            } else {
                $icon = trim($iconSize . ' ' . $iconDefaults['prefix'] . $this->Theme->getIcon($options['icon']));
                if (isset($options['class'])) {
                    $options['class'] .= ' ' . $icon;
                } else {
                    $options['class'] = ' ' . $icon;
                }
                unset($options['iconInline']);
            }
            $options['escapeTitle'] = false;
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

        return parent::link($title, $url, $options);
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
            'class' => 'tab-pane fade',
        ], $options);

        return $this->formatTemplate('blockstart', [
            'attrs' => $this->templater()
                ->formatAttributes($options),
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
