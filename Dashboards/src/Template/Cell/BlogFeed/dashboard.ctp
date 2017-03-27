<div class="blogfeed">
    <?php foreach ($posts as $post): ?>
        <h5><?= $this->Html->link($post->title, $post->url, ['target' => '_blank']); ?></h5>
        <small><?= $post->date->i18nFormat(); ?></small>
    <?php endforeach; ?>
</div>
