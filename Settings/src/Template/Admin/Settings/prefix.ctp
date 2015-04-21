<?php
use Cake\Utility\Inflector;
?>
<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php
$this->CroogoHtml->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index'))
	->addCrumb($prefix, '/' . $this->request->url);
?>
<?php

echo $this->SettingsForm->create('Settings', array(
	'url' => array(
		'controller' => 'Settings',
		'action' => 'prefix',
		$prefix,
	),
	'class' => 'protected-form',
));

?>
<div class="row-fluid">

	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab($prefix, '#settings-main');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="settings-main" class="tab-pane">
				<?php
				$i = 0;
				foreach ($settings as $setting) :
					if (!empty($setting['Params']['tab'])) {
						continue;
					}
					$keyE = explode('.', $setting->key);
					$keyTitle = Inflector::humanize($keyE['1']);

					$label = ($setting->title != null) ? $setting->title : $keyTitle;

					$i = $setting->id;

					echo $this->SettingsForm->input($setting, $label);
					$i++;
				endforeach;
				?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
		<?php
		echo $this->CroogoHtml->beginBox(__d('croogo', 'Saving')) .
			$this->SettingsForm->button(__d('croogo', 'Save')) .
			$this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'btn btn-danger')) .
			$this->CroogoHtml->endBox();
		echo $this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->SettingsForm->end(); ?>
