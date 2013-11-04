<?php
/**
 * 控制器类
 * @author liuxu
 *
 */
abstract class Controller
{
	private $displayed = false;
	private $data = array();

	public function init()
	{
		session_start();
		
		
	}

	public function filter()
	{
		
	}

	public function display($tpl,$path='')
	{
		if($this->displayed) return;

		extract($this->data);

		if(empty($path))
		{
			$class = get_class($this);
			$path = strtolower(substr($class,strlen('Controller_')));
			$path = str_replace('_','/',$path);
		}

		$config = Config::load('application','app');
		$theme = isset($config['theme'])?$config['theme']:'default';

		$file1 = APP_ROOT.'theme/'.$theme.'/'.$path.'/'.$tpl.'.php';
		$file2 = APP_ROOT.'view/'.$path.'/'.$tpl.'.php';
		if(is_file($file1)) include $file1;
		else if(is_file($file2)) include $file2;
		else throw new Exception_Default(401, '模板文件'.$path.'/'.$tpl.'不存在');

		$this->displayed = true;
	}

	protected function assign($name,$value)
	{
		$this->data[$name] = $value;
	}

	protected function createUrl($realpath,$para=array())
	{
		return Request::createUrl($realpath,$para);
	}
	

}

?>