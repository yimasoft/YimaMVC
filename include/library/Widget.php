<?php

class Widget
{
	private $displayed = false;
	private $data = array();
	
	static public function load($name,$para=array())
	{
		$class = 'Widget_'.ucfirst($name);
		$widget = new $class;
		$widget->init();
		$widget->filter();
		$widget->run($para);
		$widget->display($name);
		echo "\r\n";
	}

	public function init()
	{

	}

	public function filter()
	{
	
	}

	public function run($para=array())
	{
	
	}

	public function display($tpl)
	{
		if($this->displayed) return;
	
		extract($this->data);

		$file = INC_ROOT.'widget/view/'.$tpl.'.php';
		if(is_file($file)) include $file;
		else throw new Exception_Default(401, '模板文件'.$tpl.'不存在');
	
		$this->displayed = true;
	}

	protected function assign($name,$value)
	{
		$this->data[$name] = $value;
	}


}

?>