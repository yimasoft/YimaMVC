<?php

class Cache
{
	
	public function get($key)
	{
		$group = $this->getMemGroup($key);
		$redis = Rds::getInstance($group);

		$memKey = $this->getMemKey($key);
		$value = $redis->get($memKey);
		
		return $value;
	}
	
	public function set($key,$value)
	{
		$group = $this->getMemGroup($key);
		$redis = Rds::getInstance($group);
		
		$memKey = $this->getMemkey($key);
		$result = $redis->setex($memKey,300,$value);
		
		return $result;
	}
	
	public function del($key)
	{
		$group = $this->getMemGroup($key);
		$redis = Rds::getInstance($group);
		
		$memKey = $this->getMemkey($key);
		$result = $redis->del($memKey);
		
		return $result;
	}
	
	public function version($name,$isNew=false)
	{
		$redis = Rds::getInstance('version');

		$memKey = 'version_'.$name;
		if($isNew) $version = false;
		else $version = $redis->get($memKey);
		
		if($version===false)
		{
			$version = microtime(true);
			$redis->setex($memKey,3600*24*30,$version);
		}
		
		return $version;
	}

	private function getMemKey($key)
	{
		preg_match_all('/{([^}]+)}/i',$key,$matches);
		if(isset($matches[1]) && $matches[1])
		{
			foreach($matches[1] as $name)
			{
				$version = $this->version($name);
				$key = str_replace('{'.$name.'}',$version,$key);
			}
		}
//		echo $key;
		
		return $key;
	}
	
	private function getMemGroup($key)
	{
		$group = 'default';
		$partList = explode('_',$key);
		$config = Config::load('redis');

		if(isset($config[$partList[0]])) $group = $partList[0];
		else if(isset($config[$partList[0].'_'.$partList[1]])) $group = $partList[0].'_'.$partList[1];
		else if(isset($config[$partList[0].'_'.$partList[1].'_'.$partList[2]])) $group = $partList[0].'_'.$partList[1].'_'.$partList[2];

		return $group;
	}
	
}

?>