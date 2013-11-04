<?php
/**
 * 请求类
 * @author liuxu
 *
 */
class Request
{
	static private $req;
	private $controller = 'Controller_Index';
	private $action = 'index';
	
	private function __construct()
	{
		$this->parseUri();
	}
	
	static public function getReq()
	{
		if(empty(self::$req))
		{
			self::$req = new Request();
		}
		return self::$req;
	}

	/**
	 * 解析uri
	 */
	private function parseUri()
	{
		$path = $this->getPath();

		$realpath = $this->routeMap($path);
		if(empty($realpath)) $realpath = $this->defaultMap($path);
		if(empty($realpath)) $realpath = 'error/notFound';

		//取得控制器名及方法名
		$partList = explode('/',$realpath);
		$this->action = array_pop($partList);
		$partList = array_map('strtolower',$partList);
		$partList = array_map('ucfirst',$partList);
		$this->controller = 'Controller_'.implode('_',$partList);
	}

	private function getPath()
	{
		$path = $_SERVER['REQUEST_URI'];

		//去掉baseUri,形如"/index.php"或者"/admin/index.php"
		$config = Config::load('application','app');
		if(isset($config['baseUri']) && $config['baseUri'])
		{
			$path = substr($path,strlen($config['baseUri']));
			if(substr($path,0,1)=='?') $path = substr($path,1);
		}
		if(substr($path,0,1)=='/') $path = substr($path,1);

		//去掉查询字符串
		$pos = strpos($path,'?');
		if($pos) $path = substr($path,0,$pos);
		$pos = strpos($path,'&');
		if($pos) $path = substr($path,0,$pos);
	
		return $path;
	}

	/**
	 * 路由路径映射
	 * @param string $path
	 * @return false|string
	 */
	private function routeMap($path)
	{
		$route = Config::load('route','app');
		if(empty($route)) return false;

		$ruleList = array();
		foreach($route as $rule=>$value)
		{
			$pattern = preg_replace('/<[^>]+>/','([^/]+)',$rule);
			$pattern = preg_replace('/\//','\/',$pattern);
			if(preg_match('/^'.$pattern.'($|\/)/',$path))
			{
				$num = preg_match_all('/\//',$rule,$matches);
				$ruleList[$num][$rule]['realpath'] = $value;
				$ruleList[$num][$rule]['pattern'] = $pattern;
			}
		}
		if(empty($ruleList)) return false;

		$max = max(array_keys($ruleList));
		$rule = key($ruleList[$max]);
		$realpath = $ruleList[$max][$rule]['realpath'];
		$pattern = $ruleList[$max][$rule]['pattern'];

		preg_match('/^'.$pattern.'/',$rule,$matches1);
		preg_match('/^'.$pattern.'/',$path,$matches2);
		$count = count($matches1);
		for($i=1;$i<$count;$i++)
		{
			$name = substr($matches1[$i],1,-1);
			$_GET[$name] = $matches2[$i];
		}

		$pathPartList = explode('/',$path);
		$len = count($pathPartList);
		for($i=$max+1;$i<$len;$i+=2)
		{
			if(isset($pathPartList[$i+1]))
			{
				$_GET[$pathPartList[$i]] = $pathPartList[$i+1];
			}
		}

		return $realpath;
	}

	/**
	 * 默认路径映射
	 * @param string $path
	 * @return array
	 */
	private function defaultMap($path)
	{
		if(empty($path)) return 'index/index';

		$realpath = '';
		$controller = 'Controller';
		$pathPartList = explode('/',$path);
		foreach($pathPartList as $key=>$part)
		{
			$controller .= '_'.ucfirst(strtolower($part));
		
			if(class_exists($controller)===false) continue;//此控制器未定义
		
			if(!isset($pathPartList[$key+1])) continue;//未匹配到方法
			$action = $pathPartList[$key+1];
				
			$funcList = get_class_methods($controller);
			if(!in_array($action,$funcList)) continue;//此方法未定义
		
			//匹配成功
			$realpath = strtolower(str_replace('_','/',substr($controller,strlen('Controller_')))).'/'.$action;
			break;
		}
		if(empty($realpath)) return false;

		$len = count($pathPartList);
		for($i=$key+2;$i<$len;$i+=2)
		{
			if(isset($pathPartList[$i]) && isset($pathPartList[$i+1]))
			{
				$_GET[$pathPartList[$i]] = $pathPartList[$i+1];
			}
		}

		return $realpath;
	}

	public function getController()
	{
		return $this->controller;
	}
	
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * 动态生成url
	 * @param string $realpath
	 * @param array $para
	 * @return string
	 */
	static public function createUrl($realpath,$para=array())
	{
		$lastPath = $realpath;
		$route = Config::load('route','app');
		$ruleList = array_keys($route,$realpath);
		if($ruleList)
		{
			$pathList = $search = $replace = array();
			if($para)
			{
				$search = array_keys($para);
				$replace = array_values($para);
				$search = explode(',','<'.implode('>,<',$search).'>');
			}

			//查找符合条件的规则
			foreach($ruleList as $rule)
			{
				$path = str_replace($search,$replace,$rule,$count);
				if(!preg_match('/<[^>]+>/',$path))//替换后无占位符,符合条件
				{
					$pathList[$count][$rule] = $path;
				}
			}

			//查找最符合条件的规则
			if($pathList)
			{
				$max = max(array_keys($pathList));
				$rule = key($pathList[$max]);
				$lastPath = $pathList[$max][$rule];

				//剔出已经使用过的名值对
				preg_match_all('/<([^>]+)>/',$rule,$matches);
				if(isset($matches[1]))
				{
					foreach($matches[1] as $name)
					{
						unset($para[$name]);
					}
				}
			}
		}

		//追加名值对
		if($para)
		{
			foreach($para as $name=>$value)
			{
				$lastPath .= '/'.$name.'/'.$value;
			}
		}

		//拼装url
		$config = Config::load('application','app');
		$url = isset($config['baseUri'])?$config['baseUri']:'';
		$url .= '/'.$lastPath;
	
		return $url;
	}
}

?>