<?php
//constants
$www = 'https://dev.webrtc.com';
$rewrite_cut = 'dev.webrtc.com/';

$socketio_url = "https://localhost:8080";
$api_url = $www . '/api';
$dbs = array();

$db = array();
$db['hostname']='localhost';
$db['username']='root';
$db['password']='';
$db['dbname']='webrtc';

$dbs['master'] = $db;
