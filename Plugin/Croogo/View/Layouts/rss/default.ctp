<?php
if (!isset($channel)) {
	$channel = array();
}
if (!isset($channel['title'])) {
	$channel['title'] = $title_for_layout . ' - ' . Configure::read('Site.title');
}
?>
<?php echo '<?'; ?>xml-stylesheet type="text/xsl" href="<?php echo $this->Rss->webroot('css/feed.xsl') ?>" ?>
<?php
$channelEl = $this->Rss->channel(array(), $channel, $items);
echo $this->Rss->document(array(), $channelEl);
?>