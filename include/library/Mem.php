<?php
/**
 * memcache封装类
 * @author liuxu
 *
 */
class Mem extends Memcache
{
	static private $servers;
	
	public function __construct($group='default')
	{
		$config = Config::load('memcache');
		if(!isset($config[$group])) throw new Exception_Default(201,'memcache配置不存在'.$group.'组');
		$config = $config[$group];
		foreach($config as $value)
		{
			$this->addServer($value['host'],$value['port']);
		}
	}
	
	static public function getInstance($group='default')
	{
		if(!isset(self::$servers[$group]))
		{
			self::$servers[$group] = new Mem($group);
		}
		
		return self::$servers[$group];
	}
	
}


?>