<?php
$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Themes'), '/' . $this->request->url);

?>
<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>

<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(__d('croogo', 'Upload'),
		array('action' => 'add')
	);
?>
<?php $this->end(); ?>

<div class="row-fluid">
	<div class="span12 extensions-themes">

		<div class="current-theme row-fluid">
			<div class="screenshot span4">
				<h3><?php echo __d('croogo', 'Current Theme'); ?></h3>
				<?php
					$currentTheme = Sanitize::clean($currentTheme);
					if (isset($currentTheme['screenshot'])):
						if (!Configure::read('Site.theme')) :
							echo $this->Html->image($currentTheme['screenshot'], array('class' => 'img-polaroid'));
						else:
							echo $this->Html->tag('div',
								$this->Html->image('/theme/' . Configure::read('Site.theme') . '/img/' . $currentTheme['screenshot'], array('class' => 'img-polaroid')),
								array('class' => 'screenshot')
							);
						endif;
					endif;
				?>
			</div>

			<div class="span8">
				<h3>
				<?php
					$author = isset($currentTheme['author']) ? $currentTheme['author'] : null;
					if (isset($currentTheme['authorUrl']) && strlen($currentTheme['authorUrl']) > 0) {
						$author = $this->Html->link($author, $currentTheme['authorUrl']);
					}
					echo $currentTheme['name'];
					if (!empty($author)):
						echo ' ' . __d('croogo', 'by') . ' ' . $author;
					endif;
				?>
				</h3>
				<p class="description"><?php echo $currentTheme['description']; ?></p>
				<?php if (isset($currentTheme['regions'])): ?>
				<p class="regions"><?php echo __d('croogo', 'Regions supported: ') . implode(', ', $currentTheme['regions']); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="available-themes row-fluid">
			<h3><?php echo __d('croogo', 'Available Themes'); ?></h3>
			<ul>
			<?php
				$hasAvailable = false;
				$themesData = Sanitize::clean($themesData);
				foreach ($themesData as $themeAlias => $theme):
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
						if (isset($theme['screenshot'])):
							echo $this->Html->tag('div', $this->Html->image('/theme/' . $themeAlias . '/img/' . $theme['screenshot'], array('class' => 'img-polaroid')), array('class' => 'screenshot span4'));
						endif;
					}
					$author = isset($theme['author']) ? $theme['author'] : null;
					if (isset($theme['authorUrl']) && strlen($theme['authorUrl']) > 0) {
						$author = $this->Html->link($author, $theme['authorUrl']);
					}
					$out = $this->Html->tag('h3', $theme['name'] . ' ' . __d('croogo', 'by') . ' ' . $author, array());
					$out .= $this->Html->tag('p', $theme['description'], array('class' => 'description'));
					if (isset($theme['regions'])):
						$out .= $this->Html->tag('p', __d('croogo', 'Regions supported: ') . implode(', ', $theme['regions']), array('class' => 'regions'));
					endif;
					$out .= $this->Html->tag('div',
						$this->Form->postLink(__d('croogo', 'Activate'), array(
							'action' => 'activate',
							$themeAlias,
						), array(
							'button' => 'default',
							'icon' => 'bolt',
						)) .
						$this->Form->postLink(__d('croogo', 'Delete'), array(
							'action' => 'delete',
							$themeAlias,
						), array(
							'button' => 'danger',
							'icon' => 'trash',
						), __d('croogo', 'Are you sure?')),
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
