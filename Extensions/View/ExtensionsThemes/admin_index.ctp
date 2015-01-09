<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
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

<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
	<div class="extensions-themes <?php echo $this->Theme->getCssClass('columnFull'); ?>">

		<div class="current-theme <?php echo $this->Theme->getCssClass('row'); ?>">
			<div class="screenshot <?php echo $this->Theme->getCssClass('columnRight'); ?>">
				<h3><?php echo __d('croogo', 'Current Theme'); ?></h3>
				<?php
					$currentTheme = Sanitize::clean($currentTheme);
					if (isset($currentTheme['screenshot'])):
						if (!Configure::read('Site.theme')) :
							$file = $currentTheme['screenshot'];
						else:
							$file = '/theme/' . Configure::read('Site.theme') . '/img/' . $currentTheme['screenshot'];
						endif;
						$imgUrl = $this->Html->thumbnail($file);
						$link = $this->Html->link($imgUrl, $file, array(
							'escape' => false,
							'class' => 'thickbox',
						));
						echo $this->Html->tag('div', $link);
					endif;
				?>
			</div>

			<div class="<?php echo $this->Theme->getCssClass('columnLeft'); ?>">
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

		<div class="available-themes <?php echo $this->Theme->getCssClass('row'); ?>">
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
					echo '<li class="' . $this->Theme->getCssClass('columnFull') . '">';
					if ($themeAlias == 'default') {
						$imgUrl = $this->Html->thumbnail($theme['screenshot']);
						$link = $this->Html->link($imgUrl, $theme['screenshot'], array(
							'escape' => false,
							'class' => 'thickbox',
						));
						echo $this->Html->tag('div', $link, array('class' => 'screenshot ' . $this->Theme->getCssClass('columnRight')));
					} else {
						if (!empty($theme['screenshot'])):
							$file = '/theme/' . $themeAlias . '/img/' . $theme['screenshot'];
							$imgUrl = $this->Html->thumbnail($file);
							$link = $this->Html->link($imgUrl, $file, array(
								'escape' => false,
								'class' => 'thickbox',
							));
							echo $this->Html->tag('div', $link, array(
								'class' => 'screenshot ' . $this->Theme->getCssClass('columnRight'),
							));
						else:
							echo $this->Html->tag('div', '', array(
								'class' => $this->Theme->getCssClass('columnRight'),
							));
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
							'icon' => $this->Theme->getIcon('power-on'),
						)) .
						$this->Form->postLink(__d('croogo', 'Delete'), array(
							'action' => 'delete',
							$themeAlias,
						), array(
							'button' => 'danger',
							'escape' => true,
							'escapeTitle' => false,
							'icon' => $this->Theme->getIcon('delete'),
						), __d('croogo', 'Are you sure?')),
						array('class' => 'actions'));
					echo $this->Html->div($this->Theme->getCssClass('columnLeft'), $out);
					echo '</li>';
					$hasAvailable = true;
				endforeach;

				if (!$hasAvailable):
					echo $this->Html->tag('li', __d('croogo', 'No available theme'));
				endif;
			?>
			</ul>
		</div>
	</div>
</div>
