<?php

use Cake\Core\Configure;

if (!isset($channel)) {
    $channel = array();
}

if (!isset($channel['title'])) {
    $channel['title'] = $this->fetch('title') . ' - ' . Configure::read('Site.title');
}

?>
<?php echo '<?'; ?>xml-stylesheet type="text/xsl" href="<?php echo $this->Url->webroot('css/feed.xsl') ?>" ?>
<?php
$channelEl = $this->Rss->channel(array(), $channel, $items);
echo $this->Rss->document(array(), $channelEl);
?>