<?php
/**
 * 应用程序类
 * @author liuxu
 *
 */
class Application
{
	static private $app;

	private function __construct()
	{

	}

	static public function getApp()
	{
		if(empty(self::$app))
		{
			self::$app = new Application();
		}

		return self::$app;
	}

	public function mvc()
	{
		$req = Request::getReq();
		$class = $req->getController();
		$func = $req->getAction();
		$controller = new $class();
		$controller->init();
		$controller->filter();
		$controller->$func();
		$controller->display($func);
	}

}

?>