<?php
/**
 * Admin Default Theme for Croogo CMS
 *
 * @author Fahad Ibnay Heylaal <contact@fahad19.com>
 * @link http://www.croogo.org
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title_for_layout; ?> - <?php __('Croogo'); ?></title>
	<?php
		echo $this->Html->css(array(
			 'bootstrap/bootstrap',

	        // Admin styles
	        'admin/core',
	        'admin/panels', 
	        'admin/misc', 
	        'admin/theme-default',  // Theme admin panel

	        'admin/responsive',
            'bootstrap/responsive',
			/*'reset',
			'960',
			'/ui-themes/smoothness/jquery-ui.css',
			'admin',
			'thickbox',*/
		));
		echo $this->Layout->js();
		echo $this->Html->script(array(
			'jquery/jquery-1.7.2.min',
			'jquery/jquery-ui.min',
			'jquery/jquery.slug',
			// 'jquery/jquery.uuid', // Conflito com o bootstrap
			'jquery/jquery.cookie',
			// 'jquery/jquery.hoverIntent.minified',
			// 'jquery/superfish',
			// 'jquery/supersubs',
			'jquery/jquery.tipsy',
			'jquery/jquery.elastic-1.6.1.js',
			'jquery/thickbox-compressed',
			'plugins/bootstrap/bootstrap',
			'admin',
		));
		echo $scripts_for_layout;
	?>
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/ico/apple-touch-icon-57-precomposed.png">
</head>

<body>
	<?php echo $this->element('admin/header'); ?>
	<div id="croogo-wrapper" class="container-fluid">
		<div id="croogo-sidebar-stitch"></div>
      	<div id="croogo-sidebar-bg"></div>
      	<div class="row-fluid">
			
			<div class="span2">
				<?php echo $this->element("admin/navigation"); ?>
			</div>

			<!-- Main Container Start -->
	        <div id="croogo-container" class="span10">
	            <!-- Inner Container Start -->
         		<div class="container-fluid">
					<?php
						echo $this->Layout->sessionFlash();
						echo $content_for_layout;
					?>
					<?php echo $this->element('admin/footer'); ?>
				</div><!-- Inner Container End -->
	        </div><!-- Main Container End -->
	    </div>
    </div>
		
	<div class="push"></div>
	


	</body>
</html>