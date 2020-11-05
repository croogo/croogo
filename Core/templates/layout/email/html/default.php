<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <title><?= $title_for_layout;?></title>
</head>
<body>
    <?= $this->fetch('content');?>
</body>
</html>