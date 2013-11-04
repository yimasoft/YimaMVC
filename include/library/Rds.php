<?php
/**
 * redis封装类
 * @author liuxu
 *
 */
class Rds extends Redis
{
	static private $servers;
	
	public function __construct($group='default')
	{
		$config = Config::load('redis');
		if(!isset($config[$group])) throw new Exception_Default(301,'redis配置不存在'.$group.'组');
		try{
			$this->connect($config[$group]['host'],$config[$group]['port']);
			$this->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);			
		}catch(RedisException $e){
			throw new Exception_Default(302,'连接redis服务器失败');
		}
	}

	static public function getInstance($group='default')
	{
		if(!isset(self::$servers[$group]))
		{
			self::$servers[$group] = new Rds($group);
		}
		
		return self::$servers[$group];
	}

}

?>