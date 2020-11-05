<?php
declare(strict_types=1);

namespace Croogo\FileManager\Controller\Admin;

/**
 * @property \Croogo\FileManager\Model\Table\AssetUsagesTable $AssetUsages
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class AssetUsagesController extends AppController
{

    public $uses = [
        'Croogo/FileManager.AssetUsages',
    ];

    public function initialize(): void
    {
        parent::initialize();

        $excludeActions = [
            'changeType', 'unregister',
        ];
        if (in_array($this->getRequest()->getParam('action'), $excludeActions)) {
            $this->Security->setConfig('validatePost', false);
        }
    }

    public function add()
    {
        if ($this->getRequest()->getQuery()) {
            $assetId = $this->getRequest()->getQuery('asset_id');
            $model = $this->getRequest()->getQuery('model');
            $foreignKey = $this->getRequest()->getQuery('foreign_key');
            $type = $this->getRequest()->getQuery('type');

            $conditions = [
                'asset_id' => $assetId,
                'model' => $model,
                'foreign_key' => $foreignKey,
            ];
            $exist = $this->AssetUsages->find()
                ->where($conditions)
                ->count();
            if ($exist === 0) {
                $assetUsage = $this->AssetUsages->newEntity([
                    'asset_id' => $assetId,
                    'model' => $model,
                    'foreign_key' => $foreignKey,
                    'type' => $type,
                ]);
                $saved = $this->AssetUsages->save($assetUsage);
                if ($saved) {
                    $this->Flash->success('Asset added');
                }
            } else {
                $this->Flash->error('Asset already exist');
            }
        }
        $this->redirect($this->referer());
    }

    public function changeType()
    {
        $this->viewBuilder()->setClassName('Json');
        $result = true;
        $data = ['pk' => null, 'value' => null];
        if ($this->getRequest()->getData('pk') !== null) {
            $data = $this->getRequest()->getData();
        } elseif ($this->getRequest()->getQuery('pk') !== null) {
            $data = $this->getRequest()->getQuery();
        }

        $id = $data['pk'];
        $value = $data['value'];

        if (isset($id)) {
            $entity = $this->AssetUsages->get($id);
            $entity->set('type', $value);
            $result = $this->AssetUsages->save($entity);
        }
        $this->set(compact('result'));
        $this->set('_serialize', 'result');
    }

    public function unregister()
    {
        $this->viewBuilder()->setClassName('Json');
        $result = false;
        if ($id = $this->getRequest()->getData('id')) {
            $assetUsage = $this->AssetUsages->get($id);
            $result = $this->AssetUsages->delete($assetUsage);
        }
        $this->set(compact('result'));
        $this->set('_serialize', 'result');
    }
}
