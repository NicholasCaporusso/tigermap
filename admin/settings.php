<?php
class Settings{
	private static $settings=[
		'urls/app'=>'tigermap/admin',
		'db/host'=>'localhost',
		'db/user'=>'root',
		'db/password'=>'',
		'db/name'=>'fhsumap',
		
		'admin/super'=>[
			'admin@fhsu.edu'=>'supersuper',
		]
	];
	
	static function get($string=null){
		return isset($string{0}) ? (isset(self::$settings[$string]) ? self::$settings[$string] : null) : self::$settings;
	}
}