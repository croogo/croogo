<?php
$class = isset($class) ? $class : null;
$escape = isset($escape) ? $escape : true;

if ($escape):
	$message = h($message);
endif;
?>
<div class="<?php echo $class; ?>"><?php echo $message; ?></div>
