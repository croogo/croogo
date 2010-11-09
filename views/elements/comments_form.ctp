<div class="comment-form">
    <h3><?php __('Add new comment'); ?></h3>
    <?php
        $type = $types_for_layout[$node['Node']['type']];

        if ($this->params['controller'] == 'comments') {
            $nodeLink = $this->Html->link(__('Go back to original post', true) . ': ' . $node['Node']['title'], $node['Node']['url']);
            echo $this->Html->tag('p', $nodeLink, array('class' => 'back'));
        }

        $formUrl = array(
            'controller' => 'comments',
            'action' => 'add',
            $node['Node']['id'],
        );
        if (isset($parentId) && $parentId != null) {
            $formUrl[] = $parentId;
        }

        echo $this->Form->create('Comment', array('url' => $formUrl));
            if ($this->Session->check('Auth.User.id')) {
                echo $this->Form->input('Comment.name', array(
                    'label' => __('Name', true),
                    'value' => $this->Session->read('Auth.User.name'),
                    'readonly' => 'readonly',
                ));
                echo $this->Form->input('Comment.email', array(
                    'label' => __('Email', true),
                    'value' => $this->Session->read('Auth.User.email'),
                    'readonly' => 'readonly',
                ));
                echo $this->Form->input('Comment.website', array(
                    'label' => __('Website', true),
                    'value' => $this->Session->read('Auth.User.website'),
                    'readonly' => 'readonly',
                ));
                echo $this->Form->input('Comment.body', array('label' => false));
            } else {
                echo $this->Form->input('Comment.name', array('label' => __('Name', true)));
                echo $this->Form->input('Comment.email', array('label' => __('Email', true)));
                echo $this->Form->input('Comment.website', array('label' => __('Website', true)));
                echo $this->Form->input('Comment.body', array('label' => false));
            }

            if ($type['Type']['comment_captcha']) {
                echo $this->Recaptcha->display_form();
            }
        echo $this->Form->end(__('Post comment', true));
    ?>
</div>