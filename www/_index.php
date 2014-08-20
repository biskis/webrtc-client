<?php
	//check for username.
	$user = session::get('user');
	if(!$user) {
		include_once(APP_PATH . '/www/login.php');
	} else {
		include_once(APP_PATH . '/www/app.php');
	}
	
?>