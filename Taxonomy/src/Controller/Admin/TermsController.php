<?php
declare(strict_types=1);

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Event\Event;
use Croogo\Taxonomy\Model\Table\TermsTable;
use Exception;

/**
 * Terms Controller
 *
 * @property \Croogo\Taxonomy\Model\Table\TermsTable $Terms
 * @category Taxonomy.Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
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
class TermsController extends AppController
{

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Terms';

    protected $_redirectUrl = [
        'prefix' => 'Admin',
        'plugin' => 'Croogo/Taxonomy',
        'controller' => 'Vocabularies',
        'action' => 'index',
    ];

    /**
     * Admin index
     *
     * @access public
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            return $event->getSubject()->query
                ->contain(['Taxonomies.Vocabularies']);
        });

        return $this->Crud->execute();
    }

    /**
     * Admin delete
     *
     * @param int $id
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function delete($id)
    {
        $vocabularyId = $this->request->getQuery('vocabulary_id');
        if ($vocabularyId) {
            $redirectUrl = [
                'action' => 'index',
                '?' => [
                    'vocabulary_id' => $vocabularyId,
                ],
            ];
            $this->Taxonomy->ensureVocabularyIdExists($vocabularyId);
            $this->Terms->Taxonomies->termInVocabulary($id, $vocabularyId);
        } else {
            $redirectUrl = $this->referer();
        }
        $this->Taxonomy->ensureTermExists($id);

        $success = true;
        try {
            if ($vocabularyId) {
                $success = $this->Terms->remove($id, $vocabularyId);
            } else {
                $term = $this->Terms->get($id);
                $success = $this->Terms->delete($term);
            }
        } catch (Exception $e) {
            $success = false;
            $error = $e->getMessage();
        }
        if ($success) {
            $messageFlash = __d('croogo', 'Term deleted');
            $flashMethod = 'success';
        } else {
            $messageFlash = __d('croogo', 'Term could not be deleted. Please, try again.' . ' ' . $error);
            $flashMethod = 'error';
        }
        $this->Flash->{$flashMethod}($messageFlash);

        return $this->redirect($redirectUrl);
    }

    /**
     * Implements Term edit
     *
     * @param int $id
     * @access public
     */
    public function edit($id)
    {
        $request = $this->request;
        $vocabularyId = $request->getQuery('vocabulary_id');

        $this->Taxonomy->ensureTermExists($id);
        if (isset($vocabularyId)) {
            $this->Taxonomy->ensureVocabularyIdExists($vocabularyId);
            $this->Taxonomy->ensureTaxonomyExists($id, $vocabularyId);
            $vocabulary = $this->Terms->Vocabularies->get($vocabularyId);

            $term = $this->Terms->get($id, [
                'contain' => [
                    'Taxonomies' => function ($q) use ($id, $vocabularyId) {
                        return $q->where([
                            'term_id' => $id,
                            'vocabulary_id' => $vocabularyId,
                        ]);
                    },
                ],
            ]);
            $taxonomies = $this->Terms->Taxonomies
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'title',
                    'groupField' => 'vocabulary_id',
                ])
                ->contain(['Vocabularies', 'Terms'])
                ->where([
                    'term_id' => $term->id,
                    'vocabulary_id' => $vocabularyId,
                ]);
        } else {
            $term = $this->Terms->get($id, [
                'contain' => [
                    'Taxonomies',
                ],
            ]);
            $taxonomies = collection([]);
        }

        if ($request->is('post') || $request->is('put')) {
            $term = $this->Terms->patchEntity($term, $request->getData(), [
                'associated' => 'Taxonomies',
            ]);
            if (isset($vocabularyId)) {
                $saved = $this->Terms->edit($term, $vocabularyId);
            } else {
                $this->Terms->getAssociation('Taxonomies')->setSaveStrategy('replace');
                $saved = $this->Terms->save($term);
            }
            if ($saved) {
                $this->Flash->success(__d('croogo', 'Term saved successfuly.'));
                if ($request->getData('_apply')) {
                    return $this->redirect([
                        'action' => 'edit',
                        $term->id,
                        '?' => [
                            'vocabulary_id' => $vocabularyId,
                        ],
                    ]);
                } else {
                    return $this->redirect([
                        'controller' => 'Taxonomies',
                        'action' => 'index',
                        '?' => [
                            'vocabulary_id' => $vocabularyId,
                        ],
                    ]);
                }
            } else {
                $this->Flash->error(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'));
            }
        }
        $parentTree = $this->Terms->Taxonomies->getTree($vocabulary->alias, ['taxonomyId' => true]);

        // TranslateEventHandler expects this variable to be set.
        // Typically this is set by Crud, but since we do not use Crud for this action, we have to set it manually
        $viewVar = 'term';

        $this->set(compact('taxonomies', 'vocabulary', 'parentTree', 'term', 'vocabularyId', 'viewVar'));
    }

    /**
     * Implements Term add
     *
     * @return \Cake\Http\Response|void
     */
    public function add()
    {
        $request = $this->request;
        $vocabularyId = $request->getQuery('vocabulary_id');
        $this->Taxonomy->ensureVocabularyIdExists($vocabularyId);
        $vocabulary = $this->Terms->Vocabularies->get($vocabularyId);

        $term = $this->Terms->newEntity([]);
        $taxonomies = [];

        if ($request->is('post')) {
            $existingTerm = $this->Terms->find()
                ->where(['slug' => $request->getData('slug')])
                ->first();

            $term = $existingTerm
                ? $this->Terms->patchEntity($existingTerm, $request->getData())
                : $this->Terms->patchEntity($term, $request->getData());

            $taxonomy = $this->Terms->add($term, $vocabularyId);
            if ($taxonomy) {
                $this->Flash->success(__d('croogo', 'Term saved successfuly.'));

                $redirectUrl = [
                    'action' => 'edit',
                    $term->id,
                    '?' => [
                        'vocabulary_id' => $vocabularyId,
                    ],
                ];
                if (!$term->has('_apply')) {
                    $redirectUrl = [
                        'controller' => 'Taxonomies',
                        'action' => 'index',
                        '?' => [
                            'vocabulary_id' => $vocabularyId,
                        ],
                    ];
                }

                return $this->redirect($redirectUrl);
            } else {
                $this->Flash->error(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'));
            }
        }

        $parentTree = $this->Terms->Taxonomies->getTree($vocabulary->alias, ['taxonomyId' => true]);
        $this->set(compact('taxonomies', 'vocabulary', 'term', 'parentTree', 'vocabularyId'));
    }
}
