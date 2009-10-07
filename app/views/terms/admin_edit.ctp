<div class="terms form">
    <h2><?php echo $this->pageTitle; ?></h2>

    <?php echo $form->create('Term', array('url' => array('controller' => 'terms', 'action' => 'edit', 'vocabulary' => $vocabulary)));?>
        <fieldset>
            <?php
                echo $form->input('id');
                echo $form->input('vocabulary_id');
                echo $form->input('parent_id', array('type' => 'select', 'options' => $terms, 'empty' => true));
                echo $form->input('title');
                echo $form->input('slug');
                echo $form->input('status', array());
            ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>