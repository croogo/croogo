<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="<?php echo $this->Layout->cssClass('columnFull'); ?>">
	<?php
		echo __d('croogo', 'Sort by:');
		echo ' ' . $this->Paginator->sort('id', __d('croogo', 'Id'), array('class' => 'sort'));
		echo ', ' . $this->Paginator->sort('title', __d('croogo', 'Title'), array('class' => 'sort'));
		echo ', ' . $this->Paginator->sort('created', __d('croogo', 'Created'), array('class' => 'sort'));
	?>
	</div>
</div>

<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="<?php echo $this->Layout->cssClass('columnFull'); ?>">
		<?php echo $this->element('FileManager.admin/attachments_search'); ?>
		<hr />
	</div>
</div>

<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="<?php echo $this->Layout->cssClass('columnFull'); ?>">
		<ul id="attachments-for-links">
		<?php foreach ($attachments as $attachment) { ?>
			<li>
			<?php
				echo $this->Html->link($attachment['Attachment']['title'],
					"/{$uploadsDir}/" . $attachment['Attachment']['slug'],
				array(
					'class' => 'item-choose',
					'data-chooser_type' => 'Node',
					'data-chooser_id' => $attachment['Attachment']['id'],
					'data-chooser_title' => $attachment['Attachment']['title'],
					'rel' => sprintf(
						"/{$uploadsDir}/%s",
						$attachment['Attachment']['slug']
					),
				));

				$popup = array();
				$type = __d('croogo', $attachment['Attachment']['mime_type']);
				$popup[] = array(
					__d('croogo', 'Promoted'),
					$this->Layout->status($attachment['Attachment']['promote'])
				);
				$popup[] = array(
					__d('croogo', 'Status'),
					$this->Layout->status($attachment['Attachment']['status'])
				);
				$popup[] = array(
					__d('croogo', 'Created'),
					array($this->Time->niceShort($attachment['Attachment']['created']), array('class' => 'nowrap'))
				);
				$popup = $this->Html->tag('table', $this->Html->tableCells($popup), array(
					'class' => 'table table-condensed',
				));
				$a = $this->Html->link('', '#', array(
					'class' => 'popovers action',
					'icon' => $_icons['info-sign'],
					'data-title' => $type,
					'data-trigger' => 'click',
					'data-placement' => 'right',
					'data-html' => true,
					'data-content' => h($popup),
				));
				echo $a;
			?>
			</li>
		<?php } ?>
		</ul>
		<?php echo $this->element('admin/pagination'); ?>
	</div>
</div>
<?php

$script =<<<EOF
$('.popovers').popover().on('click', function() { return false; });;
EOF;
$this->Js->buffer($script);
