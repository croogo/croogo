<?php

namespace Croogo\FileManager\Event;

use Cake\Event\EventListenerInterface;
use Croogo\Core\Croogo;

/**
 * FileManagerEventHandler
 *
 * @category Event
 * @package  Croogo.FileManager.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManagerEventHandler implements EventListenerInterface
{

/**
 * implementedEvents
 */
    public function implementedEvents()
    {
        return [
            'Controller.Links.setupLinkChooser' => [
                'callable' => 'onSetupLinkChooser',
            ],
        ];
    }

/**
 * Setup Link chooser values
 *
 * @return void
 */
    public function onSetupLinkChooser($event)
    {
        $linkChoosers = [];
        $linkChoosers['Images'] = [
            'title' => 'Images',
            'description' => 'Attachments with an image mime type.',
            'url' => [
                'plugin' => 'Croogo/FileManager',
                'controller' => 'Attachments',
                'action' => 'index',
                '?' => [
                    'chooser_type' => 'image',
                    'chooser' => 1,
                    'KeepThis' => true,
                    'TB_iframe' => true,
                    'height' => 400,
                    'width' => 600
                ]
            ]
        ];
        $linkChoosers['Files'] = [
            'title' => 'Files',
            'description' => 'Attachments with other mime types, ie. pdf, xls, doc, etc.',
            'url' => [
                'plugin' => 'Croogo/FileManager',
                'controller' => 'Attachments',
                'action' => 'index',
                '?' => [
                    'chooser_type' => 'file',
                    'chooser' => 1,
                    'KeepThis' => true,
                    'TB_iframe' => true,
                    'height' => 400,
                    'width' => 600
                ]
            ]
        ];
        Croogo::mergeConfig('Croogo.linkChoosers', $linkChoosers);
    }
}
