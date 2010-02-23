<div class="attachments form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php
        $formUrl = array('controller' => 'attachments', 'action' => 'add');
        if (isset($this->params['named']['editor'])) {
            $formUrl['editor'] = 1;
        }
    ?>
    <?php echo $form->create('Node', array('url' => $formUrl, 'type' => 'file'));?>
        <fieldset>
        <?php
            echo $form->input('Node.file', array('label' => __('Upload', true), 'type' => 'file',));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>