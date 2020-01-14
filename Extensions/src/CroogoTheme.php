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
use Croogo\Core\PluginManager;
use Croogo\Extensions\Exception\MissingThemeException;

use UnexpectedValueException;

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
        return PluginManager::instance()
            ->getPlugins('theme');
    }

    /**
     * Get the content of theme.json or composer.json file from a theme
     *
     * @param string $theme theme plugin name
     * @return array
     */
    public function getData($theme = null, $path = null)
    {
        $themeData = [
            'name' => $theme,
            'isFrontendTheme' => true,
            'isBackendTheme' => false,
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
                    'columnFull' => 'col-12',
                    'columnLeft' => 'col-lg-8',
                    'columnRight' => 'col-lg-4',
                    'container' => 'container',
                    'containerFluid' => 'container-fluid',
                    'dashboardFull' => 'col-12',
                    'dashboardLeft' => 'col-sm-6',
                    'dashboardRight' => 'col-sm-6',
                    'dashboardClass' => 'sortable-column',
                    'formInput' => 'input-block-level',
                    'imageClass' => 'img-fluid',
                    'row' => 'row',
                    'tableHeaderClass' => 'thead-light',
                    'tableClass' => 'table table-striped',
                    'tableContainerClass' => 'table-responsive',
                    'thumbnailClass' => 'img-thumbnail',
                    'tabContentClass' => 'tab-content',
                    'boxContainerClass' => 'card',
                    'boxHeaderClass' => 'card-header',
                    'boxBodyClass' => 'card-body',
                ],
                'iconDefaults' => [
                    'iconSet' => 'mdi',
                    'prefix' => 'mdi',
                    'size' => 'sm',
                ],
                'icons' => [
                    'attach' => 'paperclip',
                    'check-mark' => 'check',
                    'comment' => 'comment-alt',
                    'copy' => 'content-duplicate',
                    'create' => 'plus',
                    'delete' => 'delete',
                    'error-sign' => 'exclamation-sign',
                    'home' => 'home',
                    'info-sign' => 'info-circle',
                    'inspect' => 'search',
                    'link' => 'link',
                    'list' => 'view-list',
                    'move-down' => 'chevron-down',
                    'move-up' => 'chevron-up',
                    'power-off' => 'power-off',
                    'power-on' => 'bolt',
                    'question-sign' => 'question-sign',
                    'read' => 'eye',
                    'refresh' => 'sync',
                    'resize' => 'arrows-alt',
                    'spin' => 'spin',
                    'spinner' => 'loading',
                    'search' => 'search',
                    'success-sign' => 'ok-sign',
                    'translate' => 'flag',
                    'update' => 'pencil',
                    'upload' => 'upload',
                    'warning-sign' => 'warning-sign',
                    'x-mark' => 'cancel',
                    'user' => 'user',
                    'key' => 'key',
                    'view' => 'eye'
                ],
                'prefixes' => [
                    '' => [
                        'helpers' => [
                            'Html' => [
                                'className' => 'Croogo/Core.Html',
                            ],
                            'Form' => [
                                'className' => 'Croogo/Core.Form',
                            ],
                            'Paginator' => [
                                'className' => 'Croogo/Core.Paginator',
                            ],
                        ],
                    ],
                    'admin' => [
                        'helpers' => [
                            'Html' => [
                                'className' => 'Croogo/Core.Html',
                            ],
                            'Form' => [
                                'className' => 'Croogo/Core.Form',
                            ],
                            'Paginator' => [
                                'className' => 'Croogo/Core.Paginator',
                            ],
                            'Breadcrumbs'
                        ],
                    ],
                ],
            ],
        ];

        $themeConfigs = [
            DS . 'config' . DS . 'theme.json',
            DS . 'webroot' . DS . 'theme.json',
        ];

        if ($theme) {
            if (empty($path)) {
                try {
                    $path = Plugin::path($theme);
                } catch (MissingPluginException $exception) {
                    throw new MissingThemeException([$theme], $exception->getCode(), $exception);
                }
            }
        } else {
            $path = ROOT . DS;
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
                if (isset($manifestFile) && isset($themeData['description'])) {
                    unset($json['description']);
                }
                $themeData = Hash::merge($themeData, $json);
            }
        }

        return $themeData;
    }

    /**
     * Activate theme $alias
     *
     * @param $theme theme alias
     * @return mixed On success Setting::$data or true, false on failure
     */
    public function activate($theme, $type = 'theme')
    {
        if (!in_array($type, ['theme', 'admin_theme'])) {
            throw new BadRequestException('Invalid theme type');
        }
        $themes = $this->getThemes();
        if (!$this->getData($theme, isset($themes[$theme]) ? $themes[$theme] : null)) {
            return false;
        }

        Cache::clearAll();
        (new PluginManager())->activate($theme);
        $settings = TableRegistry::get('Croogo/Settings.Settings');

        return $settings->write('Site.' . $type, $theme);
    }

    /**
     * Delete theme
     *
     * @param string $alias Theme alias
     * @return bool true when successful, false or array or error messages when failed
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function delete($alias)
    {
        if (empty($alias)) {
            throw new InvalidArgumentException(__d('croogo', 'Invalid theme'));
        }
        $paths = App::path('Plugin', $alias);
        $paths = array_map(function ($path) use ($alias) {
            return $path . $alias;
        }, $paths);
        $folder = new Folder;

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                continue;
            }

            $themeManifest = $path . '/config/theme.json';
            if (!file_exists($themeManifest)) {
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
                $prefix = $request->getParam('prefix');
                if (isset($data['settings']['prefixes'][$prefix]['css'])) {
                    $data['settings']['css'] = Hash::merge(
                        $data['settings']['prefixes'][$prefix]['css'],
                        $data['settings']['css']
                    );
                }
            }
            $themeData[$theme] = $data;
        }

        return $themeData[$theme];
    }
}
