<?php
if(@$_SERVER['HTTP_HOST'] == 'dev.webrtc.com'){
	define('THIS_IS_DEV', true);
	define('THIS_IS_LIVE', false);
	include_once(COMMON_PATH . '/config/config_dev.php');
} else {
	print "Where i am?";
	exit;
}
