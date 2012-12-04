<?php
$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__('Themes'), $this->here);

?>
<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>

<?php $this->start('actions'); ?>
<li>
<?php
	echo $this->Html->link(__('Upload'),
		array('action' => 'add'),
		array('button' => 'default')
	);
?>
</li>
<?php $this->end(); ?>

<div class="row-fluid">
	<div class="span12 extensions-themes">

		<div class="current-theme row-fluid">
			<div class="screenshot span4">
				<h3><?php echo __('Current Theme'); ?></h3>
				<?php
					if (!Configure::read('Site.theme')) :
						echo $this->Html->image($currentTheme['screenshot'], array('class' => 'img-polaroid'));
					else:
						echo $this->Html->tag('div',
							$this->Html->image('/theme/' . Configure::read('Site.theme') . '/img/' . $currentTheme['screenshot'], array('class' => 'img-polaroid')),
							array('class' => 'screenshot')
						);
					endif;
				?>
			</div>

			<div class="span8">
				<h3>
				<?php
					$author = $currentTheme['author'];
					if (isset($currentTheme['authorUrl']) && strlen($currentTheme['authorUrl']) > 0) {
						$author = $this->Html->link($author, $currentTheme['authorUrl']);
					}
					echo $currentTheme['name'] . ' ' . __('by') . ' ' . $author;
				?>
				</h3>
				<p class="description"><?php echo $currentTheme['description']; ?></p>
				<p class="regions"><?php echo __('Regions supported: ') . implode(', ', $currentTheme['regions']); ?></p>
			</div>
		</div>

		<div class="available-themes row-fluid">
			<h3><?php echo __('Available Themes'); ?></h3>
			<ul>
			<?php
				$hasAvailable = false;
				foreach ($themesData AS $themeAlias => $theme):
					$isAdminOnly = (!isset($theme['adminOnly']) || $theme['adminOnly'] != 'true');
					$isDefault = !($themeAlias == 'default' && !Configure::read('Site.theme'));
					$display = $themeAlias != Configure::read('Site.theme') && $isAdminOnly && $isDefault;
					if (!$display):
						continue;
					endif;
					echo '<li class="span12">';
					if ($themeAlias == 'default') {
						echo $this->Html->tag('div', $this->Html->image($theme['screenshot'], array('class' => 'img-polaroid')), array('class' => 'screenshot span4'));
					} else {
						echo $this->Html->tag('div', $this->Html->image('/theme/' . $themeAlias . '/img/' . $theme['screenshot'], array('class' => 'img-polaroid')), array('class' => 'screenshot span4'));
					}
					$author = $theme['author'];
					if (isset($theme['authorUrl']) && strlen($theme['authorUrl']) > 0) {
						$author = $this->Html->link($author, $theme['authorUrl']);
					}
					$out = $this->Html->tag('h3', $theme['name'] . ' ' . __('by') . ' ' . $author, array());
					$out .= $this->Html->tag('p', $theme['description'], array('class' => 'description'));
					$out .= $this->Html->tag('p', __('Regions supported: ') . implode(', ', $theme['regions']), array('class' => 'regions'));
					$out .= $this->Html->tag('div',
						$this->Form->postLink(__('Activate'), array(
							'action' => 'activate',
							$themeAlias,
						), array(
							'button' => 'default',
							'icon' => 'bolt',
						)) .
						$this->Form->postLink(__('Delete'), array(
							'action' => 'delete',
							$themeAlias,
						), array(
							'button' => 'danger',
							'icon' => 'trash',
						), __('Are you sure?')),
						array('class' => 'actions'));
					echo $this->Html->div('span8', $out);
					echo '</li>';
					$hasAvailable = true;
				endforeach;

				if (!$hasAvailable):
					echo $this->Html->tag('li', 'No available theme');
				endif;
			?>
			</ul>
		</div>
	</div>
</div>
