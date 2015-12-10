<?php

namespace Croogo\Comments\Model\Behavior;

use App\Model\Behavior\ModelBehavior;

/**
 * CommentableBehavior
 *
 * @category Comments.Model.Behavior
 * @package  Croogo.Comments.Model.Behavior
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentableBehavior extends ModelBehavior
{

/**
 * Setup behavior
 *
 * @return void
 */
    public function setup(Model $model, $config = [])
    {
        $this->settings[$model->alias] = $config;

        $this->_setupRelationships($model);
    }

/**
 * Setup relationships
 *
 * @return void
 */
    protected function _setupRelationships(Model $model)
    {
        $model->bindModel([
            'hasMany' => [
                'Comment' => [
                    'className' => 'Comments.Comment',
                    'foreignKey' => 'foreign_key',
                    'dependent' => true,
                    'limit' => 5,
                    'conditions' => [
                        'model' => $model->alias,
                        'status' => (bool)1,
                    ],
                ],
            ],
        ], false);
    }

/**
 * beforeDelete callback
 *
 * @return boolean
 */
    public function beforeDelete(Model $model, $cascade = true)
    {
        if ($cascade) {
            if (isset($model->hasMany['Comment'])) {
                $model->hasMany['Comment']['conditions'] = '';
            }
        }
        return true;
    }

/**
 * Get Comment settings from Type
 *
 * @param Model Model instance
 * @param array $data Model data to check
 * @return bool
 */
    public function getTypeSetting(Model $model, $data)
    {
        $defaultSetting = [
            'commentable' => false,
            'autoApprove' => false,
            'spamProtection' => false,
            'captchaProtection' => false,
        ];
        if (!Plugin::loaded('Taxonomy')) {
            return $defaultSetting;
        }
        if (empty($data[$model->alias]['type'])) {
            return $defaultSetting;
        }
        $Type = ClassRegistry::init('Taxonomy.Type');
        $type = $Type->find('first', [
            'recursive' => -1,
            'conditions' => [
                $Type->escapeField('alias') => $data[$model->alias]['type'],
            ],
        ]);
        if ($type) {
            return [
                'commentable' => $type['Type']['comment_status'] == 2,
                'autoApprove' => $type['Type']['comment_approve'] == 1,
                'spamProtection' => $type['Type']['comment_spam_protection'],
                'captchaProtection' => $type['Type']['comment_captcha'],
            ];
        }
        return $defaultSetting;
    }

/**
 * Convenience method for Comment::add()
 *
 * @return bool
 * @see Comment::add()
 */
    public function addComment(Model $Model, $data, $options = [])
    {
        if (!isset($Model->id)) {
            throw new UnexpectedValueException('Id is not set');
        }
        return $Model->Comment->add($data, $Model->alias, $Model->id, $options);
    }
}
