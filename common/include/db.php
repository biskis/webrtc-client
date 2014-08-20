<?php
function db_result($sql, $care = 'master', $id = false){
	//global $time_start;
	//echopre("TIME START SQL-RESULT: " . (microtime(true) - $time_start));
	switch($care){
		case 'master':
		default:
			global $dbs;
			$db_here = $dbs['master'];			
		break;
	}
	
	$hostname 	= $db_here['hostname'];
	$username 	= $db_here['username'];
	$password 	= $db_here['password'];
	$dbname 	= $db_here['dbname'];
	
	//echopre("TIME BEFORE CONNECT-SQL: " . (microtime(true) - $time_start));
	try {
		$conn=mysql_connect($hostname, $username, $password);
	} catch(Exception $e) {
		echo "Nu se poate face legatura cu baza de date. "; 
		exit;
	}
	//echopre("TIME AFTER CONNECT-SQL: " . (microtime(true) - $time_start));}
	mysql_select_db($dbname);
	//echopre("TIME AFTER SELECT-DB-SQL: " . (microtime(true) - $time_start));

	mysql_query('SET NAMES utf8'); 
	//echopre("TIME AFTER SET-UTF8-SQL: " . (microtime(true) - $time_start));
	/*
	if(THIS_IS_DEV)
		echopre($sql);
	/**/
	
	//echopre("TIME BEFORE SQL: " . (microtime(true) - $time_start));
	$result = mysql_query($sql) ;
	//echopre("TIME AFTER SQL: " . (microtime(true) - $time_start));
	if($id)
		return mysql_insert_id();
	
	mysql_close($conn);
	//echopre("TIME END SQL-RESULT: " . (microtime(true) - $time_start));
	return $result;
}

function dbSql($sql, $care = 'master'){
	return db_result($sql, $care);
}
function dbLastId($sql, $care = 'master'){
	return db_result($sql, $care, true);
}

function dbRow($sql, $care = 'master'){
	$result = db_result($sql, $care);
	if($result)
		return mysql_fetch_assoc($result);
	else 
		return false;
}	
	
function dbRows($sql, $care = 'master'){
	$rows = array();
	try {
		$result = db_result($sql, $care);
		while($row = mysql_fetch_assoc($result)){
			$rows[] = $row;
		}
	} catch(Exception $e){
		mail('danimdy@gmail.com', 'lymin.co errr db.php - dbRows', $sql . ' ' . print_r($e, true) . ' Warning:  mysql_fetch_assoc(): supplied argument is not a valid MySQL result resource');
	}
	return $rows;
}
?>