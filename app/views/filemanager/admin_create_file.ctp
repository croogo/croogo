<div class="filemanager form">
	<h2><?php echo $title_for_layout; ?></h2>

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

	<form method="post" action="<?php echo $html->url(array('controller' => 'filemanager', 'action' => 'create_file')) . '?path=' . urlencode($path); ?>" enctype="multipart/form-data">
		<fieldset>
			<?php
                echo $form->input('Filemanager.name', array('type' => 'text'));
            ?>
		</fieldset>
	<?php echo $form->end("Submit"); ?>
</div>