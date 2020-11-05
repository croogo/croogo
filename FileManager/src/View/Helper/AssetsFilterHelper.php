<?php
declare(strict_types=1);

namespace Croogo\FileManager\View\Helper;

use Cake\Event\Event;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;
use Cake\View\View;
use Croogo\Core\Utility\StringConverter;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Croogo\Nodes\View\Helper\NodesHelper $Nodes
 */
class AssetsFilterHelper extends Helper
{

    use LogTrait;

    /**
     * @var array
     */
    public $helpers = [
        'Html',
        'Croogo/Nodes.Nodes',
    ];

    /**
     * AssetsFilterHelper constructor.
     * @param View $view
     * @param array $settings
     */
    public function __construct(View $view, $settings = [])
    {
        parent::__construct($view);
        $this->_setupEvents();
    }

    /**
     * @return void
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
     * @param Event $event
     *
     * @return string|string[]|null
     */
    public function filter(Event $event)
    {
        $result = $event->getResult();
        $content =& $result['content'];
        $options =& $result['options'];
        $converter = new StringConverter();
        $conditions = [];
        $identifier = '';
        if (isset($options['model']) && isset($options['id'])) {
            $conditions = [
                'AssetUsages.model' => $options['model'],
                'AssetUsages.foreign_key' => $options['id'],
            ];
            $identifier = $options['model'] . '.' . $options['id'];
        }

        preg_match_all('/\[(image):[ ]*([A-Za-z0-9_\-]*)(.*?)\]/i', (string)$content, $tagMatches);
        $AssetUsages = TableRegistry::getTableLocator()->get('Croogo/FileManager.AssetUsages');

        for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
            $assets = $converter->parseString('image|i', $tagMatches[0][$i]);
            $assetId = $tagMatches[2][$i];
            $conditions['AssetUsages.id'] = $assetId;
            $assetUsage = $AssetUsages->find()
                ->contain('Assets')
                ->where($conditions)
                ->cache('asset_filtered_' . $assetId, 'nodes')
                ->first();
            if (!$assetUsage) {
                $this->log(sprintf(
                    '%s - Asset not found for %s',
                    $identifier,
                    $tagMatches[0][$i]
                ));
                $regex = '/' . preg_quote($tagMatches[0][$i]) . '/';
                $content = preg_replace($regex, '', $content);
                continue;
            }

            $options = !empty($assets[$assetId]) ? $assets[$assetId] : ['class' => $this->_View->Theme->getCssClass('thumbnailClass')];
            $img = $this->Html->image($assetUsage->asset->path, $options);
            $regex = '/' . preg_quote($tagMatches[0][$i]) . '/';
            $content = preg_replace($regex, $img, $content);
        }

        return $content;
    }

    /**
     * @return void
     */
    public function afterSetNode()
    {
        $body = $this->Nodes->field('body');
        //$body = $this->filter($body, array(
        //    'model' => 'Node', 'id' => $this->Nodes->field('id')
        //));
        $body = $this->filter(new Event('Helper.Layout.beforeFilter', $this, [
            'content' => $body,
            'model' => 'Node',
            'id' => $this->Nodes->field('id'),
        ]));

        $this->Nodes->field('body', $body);
    }
}
