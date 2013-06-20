<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php
$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'index'))
	->addCrumb($prefix, '/' . $this->request->url);
?>
<?php

echo $this->Form->create('Setting', array(
	'url' => array(
		'controller' => 'settings',
		'action' => 'prefix',
		$prefix,
	),
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
					$keyE = explode('.', $setting['Setting']['key']);
					$keyTitle = Inflector::humanize($keyE['1']);

					$label = ($setting['Setting']['title'] != null) ? $setting['Setting']['title'] : $keyTitle;

					$i = $setting['Setting']['id'];
					echo
						$this->Form->input("Setting.$i.id", array(
							'value' => $setting['Setting']['id'],
						)) .
						$this->Form->input("Setting.$i.key", array(
							'type' => 'hidden', 'value' => $setting['Setting']['key']
						)) .
						$this->SettingsForm->input($setting, $label, $i);
					$i++;
				endforeach;
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
		<?php
		echo $this->Html->beginBox('Saving') .
			$this->Form->button(__d('croogo', 'Save')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'btn btn-danger')) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
