<?php

namespace Croogo\Taxonomy\Action\Admin;

use Crud\Action\BaseAction;

class TermEditAction extends BaseAction
{

    /**
     * Default config
     */
    protected $_defaultConfig = [
        'enabled' => true,
    ];

    /**
     * Implements Term edit
     *
     * @param int $id
     * @param int $vocabularyId
     * @access public
     */
    protected function _handle()
    {
        $controller = $this->_controller();
        $request = $controller->request;
        list($id) = $request->param('pass');
        $vocabularyId = $request->query('vocabulary_id');

        $response = $controller->_ensureVocabularyIdExists($vocabularyId);
        if ($response instanceof Response) {
            return $response;
        }
        $response = $controller->_ensureTermExists($id);
        if ($response instanceof Response) {
            return $response;
        }
        $response = $controller->_ensureTaxonomyExists($id, $vocabularyId);
        if ($response instanceof Response) {
            return $response;
        }

        $vocabulary = $controller->Terms->Vocabularies->get($vocabularyId);
        $term = $controller->Terms->get($id, [
            'contain' => [
                'Taxonomies',
            ],
        ]);
        $taxonomy = $controller->Terms->Taxonomies->find()
            ->where([
                'Taxonomies.term_id' => $id,
                'Taxonomies.vocabulary_id' => $vocabularyId,
            ])
            ->first();

        $controller->set('title_for_layout', __d('croogo', '%s: Edit Term', $vocabulary->title));

        if ($request->is('post') || $request->is('put')) {
            $term = $controller->Terms->patchEntity($term, $request->data);
            if ($controller->Terms->edit($term, $vocabularyId)) {
                $controller->Flash->success(__d('croogo', 'Term saved successfuly.'));
                if (isset($request->data['_apply'])) {
                    return $controller->redirect([
                        'action' => 'edit',
                        $term->id,
                        'vocabulary_id' => $vocabularyId,
                    ]);
                } else {
                    return $controller->redirect([
                        'action' => 'index',
                        'vocabulary_id' => $vocabularyId,
                    ]);
                }
            } else {
                $controller->Flash->error(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'));
            }
        }
        $parentTree = $controller->Terms->Taxonomies->getTree($vocabulary->alias, ['taxonomyId' => true]);
        $controller->set(compact('vocabulary', 'parentTree', 'term', 'taxonomy', 'vocabularyId'));
    }

}
