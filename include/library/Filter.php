<?php
/**
 * 输入过滤工具类
 * Enter description here ...
 * @author liuxu
 *
 */
class Filter{

	/**
	 * 单值过滤
	 * Enter description here ...
	 * @param str|int	$data	待过滤数据
	 * @param str		$type	数据类型
	 * @return	bool|str|int
	 */
	static public function value($data,$type){
		if($data===null || $type===''){
			return false;
		}

		switch($type){
			case 'int':		$data = filter_var($data,FILTER_VALIDATE_INT); break;
			case 'float':	$data = filter_var($data,FILTER_VALIDATE_FLOAT); break;
			case 'ip':		$data = filter_var($data,FILTER_VALIDATE_IP); break;
			case 'email':	$data = filter_var($data,FILTER_VALIDATE_EMAIL); break;
			case 'url':		$data = filter_var($data,FILTER_VALIDATE_URL); break;
			case 'mobile':	$data = filter_var($data,FILTER_VALIDATE_REGEXP,array('options'=>array('regexp'=>'/^1\d{10}$/') ) ); break;
			case '-html':	$data = strip_tags($data);break;
			case '-js':
				$data = self::delTag($data,'script');

				$data = preg_replace_callback('/<\s*([^\s>]+)\s+(.*?)\s*>/ims',array(self,'delOn'),$data);//删除html标签的on属性

				$data = preg_replace('/expression/ims','expresion',$data);//破坏掉css扩展
				$data = preg_replace('/expression_r/ims','expresion_r',$data);

				$data = preg_replace('/javascript\s*:/ims','javasscript:',$data);//破坏掉xss
				$data = preg_replace('/vbscript\s*:/ims','vbsscript:',$data);

				break;
			case '-flash':
				$data = self::delTag($data,'embed');//删除flash
				$data = self::delTag($data,'object');
				break;
			case 'hold'://保留，不处理
				
				break;
			default:
				$data = false;
				break;
		}
		return $data;
	}

	static public function input($input,$gpc='GP'){
		foreach($input as $type=>$nameList){
			foreach($nameList as $name){
				$dict[$name] = $type;
			}
		}

		$gpc = str_split($gpc,1);
		if(in_array('G',$gpc)) $_GET = self::tree($_GET,$dict);
		if(in_array('P',$gpc)) $_POST = self::tree($_POST,$dict);
		if(in_array('C',$gpc)) $_COOKIE = self::tree($_COOKIE,$dict);
		$_REQUEST = array_merge($_GET,$_POST,$_COOKIE);
	}

	static public function tree($data,$dict,$prentName=''){
		if(!is_array($data)) return false;

		foreach($data as $key=>$value){
			$name = is_numeric($key)?$prentName:$key;
			if(is_array($value)){
				$data[$key] = Filter::tree($value,$dict,$name);
			}else{
				if(isset($dict[$name])){
					$data[$key] = Filter::value($value,$dict[$name]);
				}else{
					unset($data[$key]);
				}
			}
		}

		return $data;
	}

	/**
	 * 删除html标签的on属性
	 * Enter description here ...
	 * @param array $matches
	 */
	static private function delOn($matches){
		$tag = $matches[1];
		$attrList = explode(' ',$matches[2]);
		foreach($attrList as $key=>$pair){
			list($attrName,$attrValue) = explode('=',$pair);
			if(strtolower(substr($attrName,0,2))=='on'){
				unset($attrList[$key]);
			}
		}
		$data = '<'.$tag.' '.implode(' ',$attrList).'>';
		return $data;
	}

	static private function delTag($data,$tag)
	{
		$data = preg_replace('/<\s*'.$tag.'\s*[^>]*>.*?<\s*\/\s*'.$tag.'\s*[^>]*>/ims','',$data);
		$data = preg_replace('/<\s*(\/\s*)?'.$tag.'\s*[^>]*(>)?/ims','',$data);
		return $data;
	}

}

?>