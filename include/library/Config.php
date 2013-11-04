<?php
/**
 * 配置类
 * @author liuxu
 *
 */
class Config
{
	static private $config;
	
	/**
	 * 装载配置
	 * @param string $path
	 * @throws Exception_Default
	 */
	static public function load($path,$group='inc')
	{
		if($group=='inc')
		{
			$root = INC_ROOT.'config'.DIRECTORY_SEPARATOR;
		}
		else if($group=='app')
		{
			$root = APP_ROOT.'config'.DIRECTORY_SEPARATOR;
		}

		if(!isset(self::$config[$group][$path]))
		{
			$fullPath = $root.str_replace(array('\\','/'),DIRECTORY_SEPARATOR,$path).'.php';
			if(is_file($fullPath)) $config = include($fullPath);
			else throw new Exception_Default(101,"配置文件'".$path."'不存在");

			self::$config[$group][$path] = $config;
		}
		
		return self::$config[$group][$path];
	}
	
}

?>