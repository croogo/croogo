<?php
$class = isset($params['class']) ? $params['class'] : null;
$escape = isset($params['escape']) ? $params['escape'] : true;

if (isset($params['type'])):
    switch ($params['type']):
        case 'success':
            $class .= ' alert-success';
            break;
        case 'error':
            $class .= ' alert-danger';
            break;
    endswitch;
endif;

if ($escape):
    $message = h($message);
endif;
?>
<div class="alert <?php echo $class; ?>" role="alert"><?php echo $message; ?></div>
