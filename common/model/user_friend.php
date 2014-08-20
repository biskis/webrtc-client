<?php
class user_friend extends model{
	public $table = 'users_friends';
	public $keys = array('username_me', 'username_you');
	
}
class user_friends extends model_list{}
?>