<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php
$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'index'))
	->addCrumb($prefix, $this->here);
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
			<li><a href="#settings-main" data-toggle="tab"><?php echo $prefix; ?></a></li>
		</ul>

		<div class="tab-content">

			<div id="settings-main">
			<?php
				$i = 0;
				foreach ($settings as $setting) :
					$keyE = explode('.', $setting['Setting']['key']);
					$keyTitle = Inflector::humanize($keyE['1']);

					$label = ($setting['Setting']['title'] != null) ? $setting['Setting']['title'] : $keyTitle;

					echo
						$this->Form->input("Setting.$i.id", array(
							'value' => $setting['Setting']['id'],
						)) .
						$this->Form->input("Setting.$i.key", array(
							'type' => 'hidden', 'value' => $setting['Setting']['key']
						)) .
						$this->Croogo->settingsInput($setting, $label, $i);
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
			$this->Form->button(__('Save')) .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-danger')) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
