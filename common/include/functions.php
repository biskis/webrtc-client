<?php
function check_email_address($email) {
	//check email
	preg_match('`@(.*)$`', $email, $m);
	if (in_array(@$m[1], array('grep.ro'))) { //modi si in profile_update.php @ 31
		return true;
	} else {
		$getmxrr = getmxrr(@$m[1], $mxhosts);
		if ((!$getmxrr) || (sizeof($mxhosts) == 0)) {
			return false;
			//throw new Exception('Domeniul "' . $m[1] . '" nu are MX-uri valide');
		}
	}
	return true;
}
function ip(){
	return $_SERVER['REMOTE_ADDR'];
}
function now($what = 'long', $how_many_seconds_before = 0, $time = 0){
	global $date_secunds_dif;
	$date_secunds_dif += $how_many_seconds_before;
	if($time == 0)	$time = time();
	switch($what){
		case 'short':
			return date("Y-m-d", $time - $date_secunds_dif);
		break;
		case 'day':
			return date("d", $time - $date_secunds_dif);
		break;
		case 'long':
		default:
			return date("Y-m-d G:i:s", $time - $date_secunds_dif);
		break;
	}
		
}
function echopre($ce){
	echo '<pre>';
	print_r($ce);
	echo '</pre>';
}

function echoprex($ce){
	echo '<pre>';
	print_r($ce);
	echo '</pre>';
	exit;
}
function redirect($to = false, $replace = true, $http_response_code = '302'){
	if(!$to){
		$to = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	header("Location: $to", $replace, $http_response_code);
	exit;
}

function escapeminus($str, $replace_char = '-') {
	return preg_replace('`^' . preg_quote($replace_char, '`') . '|' . preg_quote($replace_char, '`') . '$`', '', preg_replace('`[^a-zA-Z0-9]+`', $replace_char, scoate_diacriticele(strtolower($str))));
}

function scoate_diacriticele($str) {
	return str_ireplace(
		array('a', 'ã', 'â', 'A', 'Ã', 'Â', 'î', 'Î', 's', 'º', '?', 'S', 'ª', '?', 't', 'þ', '?', 'T', 'Þ', '?'),
		array('a', 'a', 'a', 'a', 'a', 'a', 'i', 'i', 's', 's', 's', 's', 's', 's', 't', 't', 't', 't', 't', 't'),
		$str
	);
}

function strstr2($haystack, $needle, $before_needle = false){
	//nu returneaza bucata din needle pt ambele cazuri
	$pos = strpos(strtolower($haystack), strtolower($needle));
	if($pos === false)
		return '';
	if($before_needle){
		return substr($haystack, 0, $pos);
	} else {
		return substr($haystack, $pos + strlen($needle));
	}
}

function humantime($datetime, $lang){
	//todo lang
	global $lang_humantime_ago, $lang_humantime_day, $lang_humantime_days, $lang_humantime_hour, $lang_humantime_hours, $lang_humantime_minute, $lang_humantime_minutes, $lang_humantime_now, $lang_humantime_second, $lang_humantime_seconds;
	$timenow = time();
	$timethan = strtotime($datetime);
	if($timethan === false || $timethan == -1) return $datetime;	//nu pot face nimic
	
	$sec_between = $timenow - $timethan;
	
	$zile 	= intval($sec_between / (60 * 60 *24));
	$ore 	= intval($sec_between / (60 * 60)) - $zile * 24;
	$minute = intval($sec_between / 60) - $ore * 60  - $zile * 24 * 60;
	$sec = $sec_between - $minute * 60 - $ore * 60 * 60 - $zile * 24 * 60 * 60;
	
	//todo translate
	$ret = '';
	if($zile > 100) 
		return @$lang_humantime_never;
	else if($zile == 1)
		return '1 ' . $lang_humantime_day . ' ' . $lang_humantime_ago;
	else if($zile > 1)
			return $zile . ' ' . $lang_humantime_days . ' ' . $lang_humantime_ago;
	else if($ore == 1)
		return '1 ' . $lang_humantime_hour . ' ' . $lang_humantime_ago;
	else if($ore > 1)
		return $ore . ' ' . $lang_humantime_hours . ' ' . $lang_humantime_ago;
	else if($minute == 1)
		return '1 ' . $lang_humantime_minute . ' ' . $lang_humantime_ago;
	else if($minute > 1)
		return $minute . ' ' . $lang_humantime_minutes . ' ' . $lang_humantime_ago;
	else if($sec == 1)
		return '1 ' . $lang_humantime_second . ' ' . $lang_humantime_ago;
	else if($sec > 1)
		return $sec . ' ' . $lang_humantime_seconds . ' ' . $lang_humantime_ago;
	else
		return $lang_humantime_now;
}	

//http://www.php.net/manual/ro/function.floatval.php
function getFloat($str, $set=FALSE) {            
	if(preg_match("/([0-9\.,-]+)/", $str, $match)) {
		// Found number in $str, so set $str that number
		$str = $match[0];
		
		if(strstr($str, ',')) {
			// A comma exists, that makes it easy, cos we assume it separates the decimal part.
			//modificat dani: schimbat din . in ,
			$str = str_replace(',', '', $str);    	// Erase thousand seps
			//$str = str_replace(',', '.', $str);    	// Convert , to . for floatval command
			
			return floatval($str);
		} else {
			// No comma exists, so we have to decide, how a single dot shall be treated
			if(preg_match("/^[0-9]*[\.]{1}[0-9-]+$/", $str) == TRUE && $set['single_dot_as_decimal'] == TRUE) {
				// Treat single dot as decimal separator
				return floatval($str);
				
			} else {
				// Else, treat all dots as thousand seps
				$str = str_replace('.', '', $str);    // Erase thousand seps
				return floatval($str);
			}                
		}
	} else{
		// No number found, return zero
		return 0;
	}
}
?>