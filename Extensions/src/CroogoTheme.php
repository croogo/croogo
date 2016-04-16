<?php

namespace Croogo\Extensions;

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Croogo\Extensions\Exception\MissingThemeException;

/**
 * CroogoTheme class
 *
 * @category Component
 * @package  Croogo.Extensions.Lib
 * @version  1.4
 * @since    1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoTheme
{

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get theme aliases (folder names)
     *
     * @return array
     */
    public function getThemes()
    {
        $themeConfigs = [
            'config' . DS . 'theme.json',
            'webroot' . DS . 'theme.json',
        ];

        $themes = [];
        foreach (Plugin::loaded() as $plugin) {
            $path = Plugin::path($plugin);

            foreach ($themeConfigs as $themeManifestFile) {
                if (!file_exists($path . $themeManifestFile)) {
                    continue;
                }

                $manifestFile = $path . $themeManifestFile;
            }

            if (file_exists($path . 'composer.json')) {
                $composerConfig = json_decode(file_get_contents($path . 'composer.json'));
                if ($composerConfig->type === 'croogo-theme') {
                    $composerJson = $path . 'composer.json';
                }
            }

            if (!isset($manifestFile) && !isset($composerJson)) {
                continue;
            }

            $themes[] = $plugin;

            unset($manifestFile);
            unset($composerJson);
        }

        return $themes;
    }

    /**
     * Get the content of theme.json or composer.json file from a theme
     *
     * @param string $theme theme plugin name
     * @return array
     */
    public function getData($theme = null)
    {
        $themeData = [
            'name' => $theme,
            'regions' => [],
            'screenshot' => null,
            'settings' => [
                'templates' => [
                    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
                    'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
                    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
                    'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
                    'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
                ],
                'css' => [
                    'columnFull' => 'col-xs-12',
                    'columnLeft' => 'col-md-8',
                    'columnRight' => 'col-md-4',
                    'container' => 'container-fluid',
                    'dashboardFull' => 'col-xs-12',
                    'dashboardLeft' => 'col-sm-6',
                    'dashboardRight' => 'col-sm-6',
                    'dashboardClass' => 'sortable-column',
                    'formInput' => 'input-block-level',
                    'imageClass' => '',
                    'row' => 'row-fluid',
                    'tableClass' => 'table table-striped',
                    'thumbnailClass' => 'img-polaroid',
                ],
                'iconDefaults' => [
                    'iconSet' => 'fa',
                    'largeIconClass' => 'fa-xl',
                    'smallIconClass' => 'fa-sm',
                ],
                'icons' => [
                    'attach' => 'paperclip',
                    'check-mark' => 'check',
                    'comment' => 'comment-alt',
                    'copy' => 'copy',
                    'create' => 'plus',
                    'delete' => 'trash',
                    'error-sign' => 'exclamation-sign',
                    'home' => 'home',
                    'info-sign' => 'info-circle',
                    'inspect' => 'zoom-in',
                    'link' => 'link',
                    'move-down' => 'chevron-down',
                    'move-up' => 'chevron-up',
                    'power-off' => 'power-off',
                    'power-on' => 'bolt',
                    'question-sign' => 'question-sign',
                    'read' => 'eye-open',
                    'refresh' => 'refresh',
                    'resize' => 'resize-small',
                    'search' => 'search',
                    'success-sign' => 'ok-sign',
                    'translate' => 'flag',
                    'update' => 'pencil',
                    'upload' => 'upload-alt',
                    'warning-sign' => 'warning-sign',
                    'x-mark' => 'remove',
                    'user' => 'user',
                    'key' => 'key',
                ],
                'prefixes' => [
                    '' => [
                        'helpers' => [
                            'Html' => [],
                            'Form' => [],
                        ],
                    ],
                    'admin' => [
                        'helpers' => [
                            'Html' => [
                                'className' => 'Croogo/Core.CroogoHtml',
                            ],
                            'Form' => [
                                'className' => 'Croogo/Core.CroogoForm',
                            ],
                            // FIXME:
                            //							'CroogoPaginator' => array(
                            //								'className' => 'Croogo/Core.CroogoPaginator',
                            //							),
                        ],
                    ],
                ],
            ],
        ];

        $themeConfigs = [
            'config' . DS . 'theme.json',
            'webroot' . DS . 'theme.json',
        ];

        try {
            $path = Plugin::path($theme);
        } catch (MissingPluginException $exception) {
            throw new MissingThemeException([$theme], $exception->getCode(), $exception);
        }

        foreach ($themeConfigs as $themeManifestFile) {
            if (!file_exists($path . $themeManifestFile)) {
                continue;
            }

            $manifestFile = $path . $themeManifestFile;
        }

        if (file_exists($path . 'composer.json')) {
            $composerJson = $path . 'composer.json';
        }

        if (!isset($manifestFile) && !isset($composerJson)) {
            return [];
        }

        if (isset($manifestFile)) {
            $json = json_decode(file_get_contents($manifestFile), true);
            if ($json) {
                $themeData = Hash::merge($themeData, $json);
            }
        }

        if (isset($composerJson)) {
            $json = json_decode(file_get_contents($composerJson), true);
            if ($json) {
                $json['vendor'] = $json['name'];
                unset($json['name']);
                $themeData = Hash::merge($themeData, $json);
            }
        }

        return $themeData;
    }

    /**
     * Get the content of theme.json file from a theme
     *
     * @param string $alias theme folder name
     * @return array
     * @deprecated use getData()
     */
    public function getThemeData($alias = null)
    {
        return $this->getData($alias);
    }

    /**
     * Activate theme $alias
     *
     * @param $theme theme alias
     * @return mixed On success Setting::$data or true, false on failure
     */
    public function activate($theme)
    {
        if (!$this->getData($theme)) {
            return false;
        }

        Cache::delete('file_map', '_cake_core_');
        $settings = TableRegistry::get('Croogo/Settings.Settings');

        return $settings->write('Site.theme', $theme);
    }

    /**
     * Delete theme
     *
     * @param string $alias Theme alias
     * @return boolean true when successful, false or array or error messages when failed
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function delete($alias)
    {
        if (empty($alias)) {
            throw new InvalidArgumentException(__d('croogo', 'Invalid theme'));
        }
        $paths = [
            APP . 'webroot' . DS . 'theme' . DS . $alias,
            APP . 'View' . DS . 'Themed' . DS . $alias,
        ];
        $folder = new Folder;
        foreach ($paths as $path) {
            if (!file_exists($path)) {
                continue;
            }
            if (is_link($path)) {
                return unlink($path);
            } elseif (is_dir($path)) {
                if ($folder->delete($path)) {
                    return true;
                } else {
                    return $folder->errors();
                }
            }
        }
        throw new UnexpectedValueException(__d('croogo', 'Theme %s not found', $alias));
    }

    /**
     * Helper method to retrieve given $theme settings
     *
     * @param string $theme Theme name
     * @return array Theme configuration data
     */
    public static function config($theme = null)
    {
        static $croogoTheme = null;
        static $themeData = [];
        if ($croogoTheme === null) {
            $croogoTheme = new CroogoTheme();
        }

        if (empty($themeData[$theme])) {
            $data = $croogoTheme->getData($theme);
            $request = Router::getRequest();
            if ($request) {
                $prefix = $request->param('prefix');
                if (isset($data['settings']['prefixes'][$prefix]['css'])) {
                    $data['settings']['css'] = Hash::merge($data['settings']['prefixes'][$prefix]['css'],
                        $data['settings']['css']);
                }
            }
            $themeData[$theme] = $data;
        }

        return $themeData[$theme];
    }
}
