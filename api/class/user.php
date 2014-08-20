<?php
/**
* User management
* @package api
* @author Dani Dudas <danimdy@gmail.com>
*/
class api_user {
	
	function save($params = array()){
		$user = session::get('user');
		if($user) {
			if(@$params['name']) $user['name'] = $params['name'];
			if(@$params['custom_username']) $user['custom_username'] = $params['custom_username'];
			if(@$params['password']) 	$user['password'] = md5($params['password']);
			if(@$params['avatar_url']) $user['avatar'] = $params['avatar_url'];
			$user->preference_set('auto_answer', @$params['auto_answer']);
			$user->preference_set('auto_save', 	@$params['auto_save']);
			$user['status'] = 'ok';
			$user->save();
			return $user->to_array();
		}
		throw new Exception('user_not_exists');
	}
	
	/*
	* text
	*/
	function search($params){
		$user = session::get('user');
		$params['text'] = trim($params['text']);
		$ret = array();
		if(is_numeric($params['text'])){
			$users_test = new users(array('filters' => array('_id' => $params['text'])));
			foreach($users_test as $user_test){
				if($user_test['_id'] != $user['_id']) {
					$ret[] = $user_test->to_array();
				}
			}
		}
		foreach(array('custom_username', 'name') as $key){
			$users_test = new users(array('filters' => array("$key LIKE '$params[text]%'")));
			foreach($users_test as $user_test){
				if($user_test['_id'] != $user['_id']) {
					$user_array = $user_test->to_array();
					if(!in_array($user_array, $ret)) {
						$ret[] = $user_array;
					}
				}
			}
		}
		return $ret;
	}
	
	/*
	* _id - me
	* friend_id = friend id
	* action = "add" | "delete"
	*/
	function friend($params){
		$me = session::get('user');
		if($me) {
			$friend = new user_friend($me['_id'], $params['friend_id']);
			if($friend->exists && $params['action'] == 'delete'){
				$friend->delete();
			} else if (!$friend->exists && $params['action'] == 'add'){
				$friend['time'] = time();
				$friend['status'] = 'ok';
				$friend->save();
			}
		} else {
		}
		$you = user::get(@$params['friend_id']);
		if($you)
			return $you->to_array();
		return false;
	}
}