<div class="filemanager form">
	<h2><?php echo $this->pageTitle; ?></h2>
	
	<div class="breadcrumb">
	<?php
        __('You are here:');

		$breadcrumb = $filemanager->breadcrumb($path);
		foreach($breadcrumb AS $pathname => $p) {
            echo $filemanager->linkDirectory($pathname, $p);
			echo DS;
		}
	?>
	</div>
	
	<form method="post" action="<?php echo $html->url(array('controller' => 'filemanager', 'action' => 'upload')) . '?path=' . urlencode($path); ?>" enctype="multipart/form-data">
		<fieldset>
			<?php
                echo $form->input('Filemanager.file', array('type' => 'file'));
            ?>
		</fieldset>
	<?php echo $form->end("Submit"); ?>
</div>