<?php
//verificam api key
	switch (@$_POST['apikey']) {
		case 'awerty':
		case 'dev':
			define('DEVICE_TYPE', 'dev');
		break;
		default:
			define('DEVICE_TYPE', 'unknown_device');
			echo "hello";
			exit;
		break;
	}
	
	foreach (glob(APP_PATH . '/api/class/*.php') as $v) {
		if (preg_match('`/([a-z]+)\\.php$`', $v, $m)) {
			require_once $v;
		}
	}
	
	if($rewrite[1] == 'json'){
		require_once(APP_PATH . '/api/json.php');
	}
	