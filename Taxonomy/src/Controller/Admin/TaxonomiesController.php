<?php
declare(strict_types=1);

namespace Croogo\Taxonomy\Controller\Admin;

/**
 * @property \Croogo\Taxonomy\Model\Table\TaxonomiesTable $Taxonomies
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
class TaxonomiesController extends AppController
{

    public function index()
    {
        $vocabularyId = $this->getRequest()->getQuery('vocabulary_id');
        $vocabulary = $this->Taxonomies->Vocabularies
            ->get($vocabularyId, [
                'contain' => 'Types',
            ]);

        $defaultType = $this->Taxonomy->getDefaultType($vocabulary);

        $taxonomies = $this->Taxonomies->find()
            ->matching('Terms', function ($q) {
                return $q->select([
                    'id', 'title', 'slug',
                ]);
            })
            ->where([
                'vocabulary_id' => $vocabularyId,
            ])
            ->order(['lft' => 'ASC']);

        foreach ($taxonomies as $taxonomy) {
            $taxonomy->indent = $this->Taxonomies->getLevel($taxonomy);
            $taxonomy->setDirty('indent', false);
        }

        $this->set(compact('vocabulary', 'taxonomies', 'defaultType'));

        if ($this->getRequest()->getQuery('links') || $this->getRequest()->getQuery('chooser')) {
            $this->render('chooser');
        }
    }

    /**
     * Admin moveup
     *
     * @param int $id
     * @param int $vocabularyId
     * @param int $step
     * @return void
     */
    public function moveUp($id = null, $vocabularyId = null, $step = 1)
    {
        $this->move('up', $id, $vocabularyId, $step);
    }

    /**
     * Admin movedown
     *
     * @param int $id
     * @param int $vocabularyId
     * @param int $step
     * @return void
     */
    public function moveDown($id = null, $vocabularyId = null, $step = 1)
    {
        $this->move('down', $id, $vocabularyId, $step);
    }

    /**
     * Move term up/down
     *
     * @param string $direction either 'up' or 'down'
     * @param int $id
     * @param int $vocabularyId
     * @param int $step
     */
    protected function move($direction, $id, $vocabularyId, $step)
    {
        $redirectUrl = ['action' => 'index', 'vocabulary_id' => $vocabularyId];
        $this->Taxonomy->ensureVocabularyIdExists($vocabularyId);
        $this->Taxonomies->Terms->setScopeForTaxonomy($vocabularyId);
        $taxonomy = $this->Taxonomies->get($id);
        $method = 'move' . ucfirst($direction);

        if ($this->Taxonomies->$method($taxonomy, $step)) {
            $messageFlash = __d('croogo', 'Moved %s successfully', $direction);
            $cssClass = ['class' => 'success'];
        } else {
            $messageFlash = __d('croogo', 'Could not move %s', $direction);
            $cssClass = ['class' => 'error'];
        }
        $this->Flash->{$cssClass['class']}($messageFlash);

        return $this->redirect($redirectUrl);
    }

    public function delete($id)
    {
        $taxonomy = $this->Taxonomies->get($id);
        $vocabularyId = $this->getRequest()->getQuery('vocabulary_id');
        $this->Taxonomy->ensureVocabularyIdExists($vocabularyId);
        if ($this->getRequest()->is('post') && isset($taxonomy)) {
            $success = $this->Taxonomies->Terms->remove($taxonomy->term_id, $vocabularyId);
            if ($success) {
                $this->Flash->success(__d('croogo', 'Taxonomy deleted successfully'));

                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'vocabulary_id' => $vocabularyId,
                    ],
                ]);
            }
        }
    }
}
