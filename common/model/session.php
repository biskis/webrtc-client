<?php
class session extends model{
	public $table = 'sessions';
	public $keys = array('_id');
	
	private static $data = array();
	
	public static function get($key = 'user'){
		if(strlen(@$_COOKIE['lses']) == 32){
			if(@self::$data[$_COOKIE['lses']])
				return self::$data[$_COOKIE['lses']];
			$session = new session($_COOKIE['lses']);
			if($session->exists){
				$user = new user($session['username']);
				if($user->exists){
					self::$data[$_COOKIE['lses']] = $user;
					return $user;
				} else {
					$session->delete();
				}
			}
		}
		return false;
	}
	
	public static function create($user){
		$session_id = md5($user['username'] . rand(1,9999) . uniqid(uniqid(md5($user['time']))) . microtime());
		//save new session
		$session = new session($session_id);
		$session['username'] = $user['username'];
		$session['time'] = time();
		$session['ip'] = ip();
		$session->save();
		
		$data[$session_id] = $user;
		$_COOKIE['lses'] = $session_id; 
		setcookie('lses', $session_id, time()+ 60*60*24*30*1, '/');
		
		return $session_id;
	}

	public static function remove(){
		if(strlen(@$_COOKIE['lses']) == 32){
			$session = new session(@$_COOKIE['lses']);
			if($session->exists){
				unset(self::$data[@$_COOKIE['lses']]);
				$session->delete();
				setcookie('lses', '', time()- 60*60*24*30*1, '/');
				return true;
			}
		}
		return false;
	}
}
class sessions extends model_list{}
