<?php

define('INC_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);

function yimaload($class)
{
	if(substr($class,0,strlen('Controller_'))=='Controller_')
	{
		$class = substr($class,strlen('Controller_'));
		$path = APP_ROOT.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.str_replace('_',DIRECTORY_SEPARATOR,$class).'.php';
		if(is_file($path)) include $path;
	}
	else if(substr($class,0,strlen('Widget_'))=='Widget_')
	{
		$class = substr($class,strlen('Widget_'));
		$path = INC_ROOT.DIRECTORY_SEPARATOR.'widget'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.str_replace('_',DIRECTORY_SEPARATOR,$class).'.php';
		if(is_file($path)) include $path;
	}
	else
	{
		$path = INC_ROOT.'library'.DIRECTORY_SEPARATOR.str_replace('_',DIRECTORY_SEPARATOR,$class).'.php';
		if(is_file($path)) include $path;
	}
}

spl_autoload_register('yimaload');

//设置PHP执行环境
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 1);

set_time_limit(300);
ini_set('memory_limit', '128M');

ini_set('date.timezone', 'Asia/Shanghai');

ini_set('default_charset','UTF-8');
mb_internal_encoding('UTF-8');

?>