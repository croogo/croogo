<div class="terms form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php
        echo $form->create('Term', array(
            'url' => '/' . $this->params['url']['url'],
        ));
    ?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><span><a href="#term-basic"><?php __('Term'); ?></a></span></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="term-basic">
                <?php
                    echo $form->input('Taxonomy.parent_id', array(
                        'options' => $parentTree,
                        'empty' => true,
                    ));
                    echo $form->input('title');
                    echo $form->input('slug', array('class' => 'slug'));
                    echo $form->input('description');
                ?>
                </div>
                <?php echo $layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>