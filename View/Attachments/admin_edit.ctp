<div class="attachments form">
	<h2><?php echo $title_for_layout; ?></h2>

<<<<<<< HEAD
    <?php echo $this->Form->create('Node', array('url' => array('controller' => 'attachments', 'action' => 'edit')));?>
    <fieldset>
        <div class="tabs">
            <ul>
                <li><a href="#node-basic"><span><?php echo __('Attachment'); ?></span></a></li>
                <li><a href="#node-info"><span><?php echo __('Info'); ?></span></a></li>
                <?php echo $this->Layout->adminTabs(); ?>
            </ul>
=======
	<?php echo $this->Form->create('Node', array('url' => array('controller' => 'attachments', 'action' => 'edit')));?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#node-basic"><span><?php __('Attachment'); ?></span></a></li>
				<li><a href="#node-info"><span><?php __('Info'); ?></span></a></li>
			</ul>
>>>>>>> 1.3-whitespace

			<div id="node-basic">
				<div class="thumbnail">
					<?php
						$fileType = explode('/', $this->data['Node']['mime_type']);
						$fileType = $fileType['0'];
						if ($fileType == 'image') {
							echo $this->Image->resize('/uploads/'.$this->data['Node']['slug'], 200, 300);
						} else {
							echo $this->Html->image('/img/icons/' . $this->Filemanager->mimeTypeToImage($this->data['Node']['mime_type'])) . ' ' . $this->data['Node']['mime_type'];
						}
					?>
				</div>

<<<<<<< HEAD
                <?php
                    echo $this->Form->input('id');
                    echo $this->Form->input('title');
                    echo $this->Form->input('excerpt', array('label' => __('Caption')));
                    //echo $this->Form->input('body', array('label' => __('Description')));
                ?>
            </div>

            <div id="node-info">
                <?php
                    echo $this->Form->input('file_url', array('label' => __('File URL'), 'value' => Router::url($this->data['Node']['path'], true), 'readonly' => 'readonly'));
                    echo $this->Form->input('file_type', array('label' => __('Mime Type'), 'value' => $this->data['Node']['mime_type'], 'readonly' => 'readonly'));
                ?>
            </div>
            <?php echo $this->Layout->adminTabs(); ?>
        </div>
    </fieldset>
    
    <div class="buttons">
    <?php
        echo $this->Form->end(__('Save'));
        echo $this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'cancel',
        ));
    ?>
    </div>
=======
				<?php
					echo $this->Form->input('id');
					echo $this->Form->input('title');
					echo $this->Form->input('excerpt', array('label' => __('Caption', true)));
					//echo $this->Form->input('body', array('label' => __('Description', true)));
				?>
			</div>

			<div id="node-info">
				<?php
					echo $this->Form->input('file_url', array('label' => __('File URL', true), 'value' => Router::url($this->data['Node']['path'], true), 'readonly' => 'readonly'));
					echo $this->Form->input('file_type', array('label' => __('Mime Type', true), 'value' => $this->data['Node']['mime_type'], 'readonly' => 'readonly'));
				?>
			</div>
		</div>
	</fieldset>
	
	<div class="buttons">
	<?php
		echo $this->Form->end(__('Save', true));
		echo $this->Html->link(__('Cancel', true), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
>>>>>>> 1.3-whitespace
</div>