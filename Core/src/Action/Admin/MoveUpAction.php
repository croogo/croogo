<?php

namespace Croogo\Core\Action\Admin;

use Cake\Event\Event;
use Crud\Action\BaseAction;
use Crud\Event\Subject;
use Crud\Traits\FindMethodTrait;
use Crud\Traits\RedirectTrait;
use Crud\Traits\SaveMethodTrait;

class MoveUpAction extends BaseAction
{
    use FindMethodTrait;
    use RedirectTrait;
    use SaveMethodTrait;

    /**
     * Default settings for 'edit' actions
     *
     * `enabled` Is this crud action enabled or disabled
     *
     * `findMethod` The default `Model::find()` method for reading data
     *
     * `view` A map of the controller action and the view to render
     * If `NULL` (the default) the controller action name will be used
     *
     * `relatedModels` is a map of the controller action and the whether it should fetch associations lists
     * to be used in select boxes. An array as value means it is enabled and represent the list
     * of model associations to be fetched
     *
     * `saveOptions` Options array used for $options argument of patchEntity() and save method.
     * If you configure a key with your action name, it will override the default settings.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'scope' => 'entity',
        'field' => 'weight',
        'findMethod' => 'all',
        'saveMethod' => 'save',
        'view' => null,
        'relatedModels' => true,
        'saveOptions' => [],
        'messages' => [
            'success' => [
                'text' => 'Successfully moved {name} up'
            ],
            'error' => [
                'text' => 'Could not move {name} up'
            ]
        ],
        'redirect' => [
            'post_add' => [
                'reader' => 'request.data',
                'key' => '_add',
                'url' => ['action' => 'add']
            ],
            'post_edit' => [
                'reader' => 'request.data',
                'key' => '_edit',
                'url' => ['action' => 'edit', ['subject.key', 'id']]
            ]
        ],
        'api' => [
            'methods' => ['put', 'post'],
            'success' => [
                'code' => 200
            ],
            'error' => [
                'exception' => [
                    'type' => 'validate',
                    'class' => '\Crud\Error\Exception\ValidationException'
                ]
            ]
        ],
        'serialize' => []
    ];

    /**
     * HTTP PUT handler
     *
     * @param mixed $id Record id
     * @return void|\Cake\Network\Response
     */
    protected function _put($id, $step = 1)
    {
        $subject = $this->_subject();
        $subject->set(['id' => $id]);

        $entity = $this->_findRecord($id, $subject);
        $entity->set($this->config('field'), $entity->get($this->config('field')) - $step);

        $this->_trigger('beforeMoveUp', $subject);
        if (call_user_func([$this->_table(), $this->saveMethod()], $entity, $this->saveOptions())) {
            return $this->_success($subject);
        }

        $this->_error($subject);
    }

    /**
     * HTTP POST handler
     *
     * Thin proxy for _put
     *
     * @param mixed $id Record id
     * @return void|\Cake\Network\Response
     */
    protected function _post($id = null)
    {
        return $this->_put($id);
    }

    /**
     * Success callback
     *
     * @param \Crud\Event\Subject $subject Event subject
     * @return \Cake\Network\Response
     */
    protected function _success(Subject $subject)
    {
        $subject->set(['success' => true, 'created' => false]);
        $this->_trigger('afterSave', $subject);

        $this->setFlash('success', $subject);

        $redirect = ['action' => 'index'];
        if ($this->_controller()->request->referer()) {
            $redirect = $this->_controller()->request->referer();
        }

        return $this->_redirect($subject, $redirect);
    }

    /**
     * Error callback
     *
     * @param \Crud\Event\Subject $subject Event subject
     * @return \Cake\Network\Response
     */
    protected function _error(Subject $subject)
    {
        $subject->set(['success' => false, 'created' => false]);
        $this->_trigger('afterSave', $subject);

        $this->setFlash('error', $subject);

        $this->_trigger('beforeRender', $subject);

        return $this->_redirect($subject, ['action' => 'index']);
    }
}
