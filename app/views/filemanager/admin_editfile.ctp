<div class="filemanager form">
	<h2><?php echo $this->pageTitle; ?></h2>
	
	<div class="breadcrumb">
	<?php
        __('You are here:');

		$breadcrumb = $filemanager->breadcrumb($path);
		foreach($breadcrumb AS $pathname => $p) {
			//echo $html->link($pathname, array('controller' => 'filemanager', 'action' => 'browse', base64_encode($p)));
            echo $filemanager->linkDirectory($pathname, $p);
			echo DS;
		}
	?>
	</div>
	
	<form method="post" action="<?php echo $html->url(array('controller' => 'filemanager', 'action' => 'editfile')) . '?path=' . urlencode($absolutefilepath); ?>">
		<fieldset>
			<?php
				echo $form->input('Filemanager.content', array('type' => 'textarea', 'value' => $content, 'class' => 'file-content'));
			?>
		</fieldset>
	<?php echo $form->end("Submit"); ?>
</div>