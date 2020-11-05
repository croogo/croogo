<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php use Cake\Core\Configure;

echo $this->Html->charset() ?>
<title><?= $title_for_layout ?></title>
<style>
p { text-align:center; font:bold 1.1em sans-serif; }
a { color:#444; text-decoration:none; }
a:hover { text-decoration: underline; color:#44E; }
</style>
</head>
<body>
<p><?= __d('croogo', 'Site down for maintenance.') ?></p>
</body>
</html>
<?php Configure::write('debug', 0) ?>
