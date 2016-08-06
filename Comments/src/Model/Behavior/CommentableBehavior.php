<?php

namespace Croogo\Comments\Model\Behavior;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Croogo\Nodes\Model\Entity\Node;

/**
 * CommentableBehavior
 *
 * @category Comments.Model.Behavior
 * @package  Croogo.Comments.Model.Behavior
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentableBehavior extends Behavior
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->_table->hasMany('Comments', [
            'className' => 'Croogo/Comments.Comments',
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'model' => App::shortName(get_class($this->_table), 'Model/Table', 'Table'),
                'status' => 1
            ],
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);
        $this->_table->hasMany('AllComments', [
            'className' => 'Croogo/Comments.Comments',
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'model' => App::shortName(get_class($this->_table), 'Model/Table', 'Table'),
            ],
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);
        $this->_table->Comments->belongsTo($this->_table->alias(), [
            'className' => App::shortName(get_class($this->_table), 'Model/Table', 'Table'),
            'foreignKey' => 'foreign_key'
        ]);
    }

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
 * Get Comment settings from Type
 *
 * @param Model Model instance
 * @param array $data Model data to check
 * @return bool
 */
    public function getTypeSetting(Node $node)
    {
        $defaultSetting = [
            'commentable' => false,
            'autoApprove' => false,
            'spamProtection' => false,
            'captchaProtection' => false,
        ];
        if (!Plugin::loaded('Croogo/Taxonomy')) {
            return $defaultSetting;
        }
        if (empty($node->type)) {
            return $defaultSetting;
        }

        $types = TableRegistry::get('Croogo/Taxonomy.Types');
        $type = $types->find()->where([
            $types->aliasField('alias') => $node->type,
        ])->first();
        if (!$type) {
            return $defaultSetting;
        }

        return [
            'commentable' => $type->comment_status == 2,
            'autoApprove' => $type->comment_approve == 1,
            'spamProtection' => $type->comment_spam_protection,
            'captchaProtection' => $type->comment_captcha,
        ];
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
