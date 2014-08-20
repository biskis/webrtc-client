<?php
	header('Content-type: application/json');
	
	$class_name = "api_" . escapeminus($rewrite[2], '_');
	$method_name = @$rewrite[3];
	$microtime_start = microtime(true);
	
	$ret = array('result' => false, 'error' => false);
	
	if($class_name && class_exists($class_name, false)){
		$instance = new $class_name();

		try {
			$method_name = escapeminus($method_name, '_');
			if (!method_exists($instance, $method_name)) throw new Exception('method_does_not_exist');
			
			$ret['result']	= call_user_func_array(array($instance, $method_name), array($_POST));
			
		} catch (Exception $e) {
			$ret['error']	= array(
				'code'		=> $e->getCode(),
				'message'	=> $e->getMessage(),
				'source'	=> $e->getFile() . '@' . $e->getLine(),
			);
		}
	} else {
		$ret['error'] = array(
			'code'		=> '-1',
			'message'	=> 'class_not_found',
		);
	}
	
	if(@$ret['error'] && @$errors_generic){
		$ret['error']['human'] = (@$errors_generic[$ret['error']['message']]) ? $errors_generic[$ret['error']['message']]['human'] : "Sorry, this is an error. Please report it and we'll fix it. (" . $ret['error']['message'] . ")";
	}
	
	echo json_encode($ret);