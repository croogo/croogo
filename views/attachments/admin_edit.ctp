<div class="attachments form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php echo $form->create('Node', array('url' => array('controller' => 'attachments', 'action' => 'edit')));?>
        <fieldset>

            <div class="tabs">
                <ul>
                    <li><a href="#node-basic"><span><?php __('Attachment'); ?></span></a></li>
                    <li><a href="#node-info"><span><?php __('Info'); ?></span></a></li>
                </ul>
            
                <div id="node-basic">
                    <div class="thumbnail">
                        <?php
                            $fileType = explode('/', $this->data['Node']['mime_type']);
                            $fileType = $fileType['0'];
                            if ($fileType == 'image') {
                                echo $image->resize('/uploads/'.$this->data['Node']['slug'], 200, 300);
                            } else {
                                echo $html->image('/img/icons/' . $filemanager->mimeTypeToImage($this->data['Node']['mime_type'])) . ' ' . $this->data['Node']['mime_type'];
                            }
                        ?>
                    </div>

                    <?php
                        echo $form->input('id');
                        echo $form->input('title');
                        echo $form->input('excerpt', array('label' => __('Caption', true)));
                        //echo $form->input('body', array('label' => __('Description', true)));
                    ?>
                </div>

                <div id="node-info">
                    <?php
                        echo $form->input('file_url', array('label' => __('File URL', true), 'value' => Router::url($this->data['Node']['path'], true), 'readonly' => 'readonly'));
                        echo $form->input('file_type', array('label' => __('Mime Type', true), 'value' => $this->data['Node']['mime_type'], 'readonly' => 'readonly'));
                    ?>
                </div>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>