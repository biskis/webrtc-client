<?php
$time_start = microtime(true);
//config
	define('COMMON_PATH', dirname(__FILE__));
	define('APP_PATH', dirname(dirname(__FILE__)));
	include_once(COMMON_PATH . "/config/config.php");
//includes
	include_once(COMMON_PATH . "/include/db.php");
	include_once(COMMON_PATH . "/include/functions.php");
	include_once(COMMON_PATH . "/include/errors.php");
//rewrite
	$url = "https://" . @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'];
	if($_SERVER['REQUEST_SCHEME'] == 'http' || @$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'http')
		redirect($url);
	$url = urldecode($url);
	$url_full = $url;
	if(strpos($url, $rewrite_cut) !== false)
		$url = substr(strstr($url, $rewrite_cut), strlen($rewrite_cut));
	if(strpos($url, '?') !== false)
		$url = substr($url, 0, strpos($url, '?'));
	if(substr($url,-1) == '/') 
		$url = substr($url, 0,-1);
	$rewrite = explode("/", $url);
	
//classes
	$all_classes = glob(COMMON_PATH . "/class/*.php");
	foreach($all_classes as $class){
		if(is_file($class))
			include_once($class);
	}
//model
	$all_models = glob(COMMON_PATH . "/model/*.php");
	foreach($all_models as $model){
		if(is_file($model)) {
			include_once($model);
		}
	}
	
//now include something
if($rewrite[0] == 'api') {
	require_once(APP_PATH . "/api/_index.php");
} else {
	require_once(APP_PATH . "/www/_index.php");
}
