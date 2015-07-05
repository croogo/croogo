<div class="blogfeed">
	<?php foreach ($posts as $post): ?>
		<h1><?= $this->Html->link($post->title, $post->url, ['target' => '_blank']); ?></h1>
		<small><?= $post->date->i18nFormat(); ?></small>
	<?php endforeach; ?>
</div>
