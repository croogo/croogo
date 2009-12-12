<?php
    $javascript->link(array('terms'), false);
?>
<div class="terms form">
    <h2><?php echo $this->pageTitle; ?></h2>

    <?php echo $form->create('Term', array('url' => array('controller' => 'terms', 'action' => 'add', 'vocabulary' => $vocabulary)));?>
        <fieldset>
            <?php
                echo $form->input('vocabulary_id', array('value' => $vocabulary));
                echo $form->input('parent_id', array('type' => 'select', 'options' => $terms, 'empty' => true));
                echo $form->input('title');
                echo $form->input('slug', array('class' => 'slug'));
                echo $form->input('status', array());
            ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>