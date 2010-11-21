<div class="translate form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php
        echo $this->Form->create($modelAlias, array('url' => array(
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
                    echo $this->Form->input($modelAlias.'.'.$field);
                }
             ?>
             </div>
        </div>
    </fieldset>

    <div class="buttons">
    <?php
        echo $this->Form->end(__('Save', true));
    ?>
    </div>
</div>