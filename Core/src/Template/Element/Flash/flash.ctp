<?php
$class = isset($params['class']) ? $params['class'] : null;
$escape = isset($params['escape']) ? $params['escape'] : true;

if ($escape):
	$message = h($message);
endif;
?>
<div class="<?php echo $class; ?>"><?php echo $message; ?></div>
