<?php

// Contact
CroogoRouter::connect('/contact', array(
	'plugin' => 'contacts', 'controller' => 'contacts', 'action' => 'view', 'contact'
));
