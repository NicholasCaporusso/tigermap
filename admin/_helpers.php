<?php

class Session{
	static function requireUser($API=false,$top_level=null,$user_level=null){
		if(!self::isUser($top_level,$user_level)){
			if($API) die('user is not authenticated');
			else header('location: index.php');
		}
	}
	
	static function isUser($top_level=null,$user_level=null){
		if(!isset($_SESSION['user/ID'])) return false;
		if(is_numeric($user_level)){
			foreach($_SESSION['user/roles'] as $role) if($role>=$user_level) return true;
			else return false;
		}
		if(is_numeric($top_level) && $_SESSION['user/role']<$top_level) return false;
		return true;
	}
	
	static function signOut(){
		$_SESSION=[];
		header('location:index.php');
	}
}

class DB{
	private static $db;
	static function get(){
		if(!isset(self::$db)){
			self::$db=new mySQL();
			self::$db->connect(Settings::get('db/host'),Settings::get('db/name'),Settings::get('db/user'),Settings::get('db/password'));
		}
		return self::$db;
	}
}

class Roles{
	const USER=0;
	const AUTHOR=1;
	const EDITOR=2;
	const ADMIN=3;
	const SUPER=4;
}


class Template{
	private static $data=[];
	
	static function get($key=null){
		return !isset($key{0}) ? self::$data : (isset(self::$data[$key]) ? self::$data[$key] : null);
	}
	
	static function set($key,$value){
		self::$data[$key]=$value;
	}
}