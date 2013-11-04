<?php

class Model2 extends Model
{
	private $cache;
	
	public function __construct()
	{
		parent::__construct();

		$this->cache = new Cache();
		
	}
	
	public function get($id)
	{
		$memKey = 'model_'.$this->dbname.'_'.$this->tablename.'_info_'.$id;
		$info = $this->cache->get($memKey);
		if($info===false)
		{
			$info = parent::get($id);
			$this->cache->set($memKey,$info);
		}

		return $info;
	}
	
	public function set($id,$info)
	{
		$result = parent::set($id,$info);
		if($id==0) $id = $result;

		$memKey = 'model_'.$this->dbname.'_'.$this->tablename.'_info_'.$id;
		$this->cache->del($memKey);

		$name = 'model_'.$this->dbname.'_'.$this->tablename;
		$this->cache->version($name,true);

		return $result;
	}
	
	public function del($id)
	{
		$result = parent::del($id);
		
		$memKey = 'model_'.$this->dbname.'_'.$this->tablename.'_info_'.$id;
		$this->cache->del($memKey);
		
		$name = 'model_'.$this->dbname.'_'.$this->tablename;
		$this->cache->version($name,true);
		
		return $result;
	}
	
	public function select($para)
	{
		$memKey = 'model_'.$this->dbname.'_'.$this->tablename.':{model_'.$this->dbname.'_'.$this->tablename.'}_list_'.md5(serialize($para));
		$list = $this->cache->get($memKey);
		if($list===false)
		{
			$list = parent::select($para);
			$this->cache->set($memKey,$list);
		}
		
		return $list;
	}
}

?>