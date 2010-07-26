<div class="links form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('Link', array('url' => array('controller' => 'links', 'action' => 'edit', 'menu' => $menuId)));?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#link-basic"><span><?php __('Link'); ?></span></a></li>
                    <li><a href="#link-access"><span><?php __('Access'); ?></span></a></li>
                    <li><a href="#link-misc"><span><?php __('Misc.'); ?></span></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="link-basic">
                    <?php
                        echo $form->input('id');
                        echo $form->input('menu_id');
                        echo $form->input('parent_id', array(
                            'label' => __('Parent', true),
                            'options' => $parentLinks,
                            'empty' => true,
                        ));
                        echo $form->input('title');
                        echo $form->input('link') . 
                            $html->link(__('Link to a Node', true), Router::url(array(
                                'controller' => 'nodes',
                                'action' => 'index',
                                'links' => 1,
                            ), true) . '?KeepThis=true&TB_iframe=true&height=400&width=600',
                            array(
                                'class' => 'thickbox',
                            ));
                        echo $form->input('status');
                    ?>
                </div>

                <div id="link-access">
                    <?php
                        echo $form->input('Role.Role');
                    ?>
                </div>

                <div id="link-misc">
                    <?php
                        echo $form->input('description');
                        echo $form->input('rel');
                        echo $form->input('target');
                        echo $form->input('params');
                    ?>
                </div>
                <?php echo $layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>