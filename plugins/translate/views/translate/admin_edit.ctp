<div class="translate form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php
        echo $form->create($modelAlias, array('url' => array(
            'controller' => 'translate',
            'action' => 'edit',
            $modelAlias,
            'locale' => $this->params['named']['locale'],
        )));
    ?>
    <fieldset>
        <div class="tabs">
            <ul>
                <li><a href="#record-main"><span><?php __('Record'); ?></span></a></li>
            </ul>

            <div id="record-main">
            <?php
                foreach ($fields AS $field) {
                    echo $form->input($modelAlias.'.'.$field);
                }
             ?>
             </div>
        </div>
    </fieldset>
    <?php echo $form->end('Submit');?>
</div>