<?php
/**
 * 错误消息类
 * @author liuxu
 *
 */
class LastError{
	static private $code;
	static private $message;
	
	static public function set($code,$message){
		self::$code		= $code;
		self::$message	= $message;
	}
	
	static public function get(){
		return array('code'=>self::$code,'message'=>self::$message);
	}
	
	static public function getCode(){
		return self::$code;
	}
	
	static public function getMessage(){
		return self::$message;
	}
	
}

?>