<?php

class Model extends Table
{
	protected $group = 'default';
	protected $dbname;
	protected $tablename;
	protected $pkname = '';

	public function get($id)
	{
		$para['where']['para'] = array(':id'=>$id);
		$para['where']['where'] = $this->pkname."=':id'";

		$result = $this->select($para);
		if($result) return $result[0];
		else return false;
	}

	public function set($id=0,$info)
	{
		$para['set'] = $info;
		
		if($id)
		{
			$para['where']['para'] = array(':id'=>$id);
			$para['where']['where'] = $this->pkname."=':id'";
				
			return $this->update($para);
		}
		else
		{
			return $this->insert($para);
		}
	}

	public function del($id)
	{
		$para['where']['para'] = array(':id'=>$id);
		$para['where']['where'] = $this->pkname."=':id'";
		
		return $this->delete($para);
	}
	
	

}

?>