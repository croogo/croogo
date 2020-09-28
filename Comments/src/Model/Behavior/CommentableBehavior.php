<?php
declare(strict_types=1);

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
    public function initialize(array $config): void
    {
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
        $this->_table->Comments->belongsTo($this->_table->getAlias(), [
            'className' => App::shortName(get_class($this->_table), 'Model/Table', 'Table'),
            'foreignKey' => 'foreign_key'
        ]);

        if (!$this->_table->Comments->behaviors()->has('CounterCache')) {
            $this->_table->Comments->addBehavior('CounterCache', [
                $this->_table->getAlias() => ['comment_count'],
            ]);
        }
    }

    /**
     * Get Comment settings from Type
     *
     * @param Node $node Model data to check
     * @return array
     */
    public function getTypeSetting(Node $node)
    {
        $defaultSetting = [
            'commentable' => false,
            'autoApprove' => false,
            'spamProtection' => false,
            'captchaProtection' => false,
        ];
        if (!Plugin::isLoaded('Croogo/Taxonomy')) {
            return $defaultSetting;
        }
        if (empty($node->type)) {
            return $defaultSetting;
        }

        $types = TableRegistry::getTableLocator()->get('Croogo/Taxonomy.Types');
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

}
