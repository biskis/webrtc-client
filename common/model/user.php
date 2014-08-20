<?php
class user extends model{
	public $table = 'users';
	public $keys = array('_id');
	public $autoincrement = true;
	
	
	public static function get($id){
		//by _id
		$user_test = new user($id);
		if($user_test->exists) {
			return $user_test;
		}
		//by custom_username
		$users_test = new users(array('filters' => array('custom_username' => $id)));
		foreach($users_test as $user_test){
			return $user_test;
		}
		
		return false;
	}
	
	public function username(){
		return $this['_id'];
	}
	
	public function to_array() {
		$ret = array(
			'_id'				=> (string)$this['username'],
			'username'			=> (string)$this['username'],
			'custom_username'	=> (string)$this['custom_username'],
			'name' 				=> (string)($this['name']) ?:(($this['custom_username'])?:($this['_id'])),
			'avatar'			=> $this->avatar_url(),
			'status'			=> (string)$this['status'],
			'is_friend'			=> false,
		);
		//verificam daca e prieten.
		$me = session::get('user');
		if($me) {
			$friend = new user_friend($me['_id'], $this['_id']);
			if($friend->exists){
				$ret['is_friend'] = true;
			}
		}
		return $ret;
	}
	
	public function avatar_url(){
		return ($this['avatar']) ?: "/static/img/default_avatar.png";
	}
	
	public function friends(){
		$ret = array();
		$friends = new user_friends(array('filters' => array('username_me' => $this['_id'])));
		foreach($friends as $friend){
			$friend_user = new user($friend['username_you']);
			$ret[$friend_user['_id']] = $friend_user->to_array();
		}
		return $ret;
	}

	public function preference($key){
		return $this[$key];
	}
	
	public function preference_set($key, $value){
		$this[$key] = (int)$value;
		$this->save();
	}
	
	public function get_settings(){
		return array(
			'auto_answer' => (boolean)$this->preference('auto_answer'),
			'auto_save' => (boolean)$this->preference('auto_save'),
		);
	}
}
class users extends model_list{}
?>