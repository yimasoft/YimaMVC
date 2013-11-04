<?php
/**
 * 默认异常处理类
 * @author liuxu
 *
 */
class Exception_Default extends Exception
{
	
	public function __construct($code,$message)
	{
		$file = implode(DIRECTORY_SEPARATOR, array_slice(explode(DIRECTORY_SEPARATOR,$this->file), -2));
		
		header("Content-type: text/plain; charset=utf-8"); 
		
		echo "文件".$file."第".$this->line."行，[".$code."]".$message;
		exit;
	}
	
}

?>