<?php

App::uses('ModelBehavior', 'Model/Behavior');

/**
 * TaxonomizableBehavior
 *
 * @category Taxonomy.Model.Behavior
 * @package  Croogo.Taxonomy.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomizableBehavior extends ModelBehavior {

/**
 * Setup behavior
 *
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config;

		$this->_setupRelationships($model);
	}

/**
 * Setup relationships
 *
 * @return void
 */
	protected function _setupRelationships(Model $model) {
		$model->bindModel(array(
			'hasAndBelongsToMany' => array(
				'Taxonomy' => array(
					'className' => 'Taxonomy.Taxonomy',
					'with' => 'Taxonomy.ModelTaxonomy',
					'foreignKey' => 'foreign_key',
					'associationForeignKey' => 'taxonomy_id',
					'unique' => true,
					'conditions' => array(
						'model' => $model->alias,
					),
				),
			),
		), false);

		$model->Taxonomy->bindModel(array(
			'hasAndBelongsToMany' => array(
				$model->alias => array(
					'className' => $model->plugin . '.' . $model->alias,
					'with' => 'Taxonomy.ModelTaxonomy',
					'foreignKey' => 'foreign_key',
					'associationForeignKey' => 'taxonomy_id',
					'unique' => true,
					'conditions' => array(
						'model' => $model->alias,
					),
				),
			),
		), false);
	}

/**
 * beforeSave
 *
 * @return bool
 */
	public function beforeSave(Model $model, $options = array()) {
		$model->cacheTerms();
		return true;
	}

/**
 * Caches Term in `terms` field
 *
 * @return void
 */
	public function cacheTerms(Model $model) {
		if (isset($model->data['Taxonomy']['Taxonomy'])) {
			$taxonomyIds = $model->data['Taxonomy']['Taxonomy'];
			$taxonomies = $model->Taxonomy->find('all', array(
				'conditions' => array(
					'Taxonomy.id' => $taxonomyIds,
				),
			));
			$terms = Hash::combine($taxonomies, '{n}.Term.id', '{n}.Term.slug');
			$model->data[$model->alias]['terms'] = $model->encodeData($terms, array(
				'trim' => false,
				'json' => true,
			));
		}
	}

}
