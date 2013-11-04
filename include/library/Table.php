<?php

class Table
{
	protected $cube;
	protected $dbname;
	protected $tablename;
	private $onlyMaster = false;
	public $count = 0;

	public function __construct()
	{
		$this->cube = new Cube($this->group);
		$this->cube->turn(1);
	}

	public function getCube()
	{
		return $this->cube;
	}

	private function getTablename()
	{
		$tablename = $this->tablename;
		if($this->cube->getTableSuffix())
		{
			$tablename .= '_'.$this->cube->getTableSuffix();
		}
	
		return $tablename;
	}
	
	public function onlyMaster()
	{
		$this->onlyMaster = true;
	}

	public function conn($type='slave')
	{
		if($type=='master')
		{
			$para = $this->cube->getMaster();
			$this->onlyMaster = true;
		}
		else if($this->onlyMaster==true)
		{
			$para = $this->cube->getMaster();
		}
		else
		{
			$para = $this->cube->getSlave();
		}
		
		$para['dbname'] = $this->dbname;
		if($this->cube->getDbSuffix())
		{
			$para['dbname'] .= '_'.$this->cube->getDbSuffix();
		}

		$conn = new Db($para['host'],$para['port'],$para['user'],$para['pass'],$para['dbname']);

		return $conn;
	}

	public function select($para)
	{
		$conn = $this->conn('slave');

		$field = isset($para['field'])?$para['field']:"*";

		$where = $this->parseWhere($para);

		$group = isset($para['group'])?("GROUP BY ".$para['group']):"";

		$having = isset($para['having'])?("HAVING ".$para['having']):"";

		$order = isset($para['order'])?("ORDER BY ".$para['order']):"";

		$limit = $this->parseLimit($para);

		$sql = "SELECT SQL_CALC_FOUND_ROWS ".$field." FROM `".$this->getTablename()."` ".$where." ".$group." ".$having." ".$order." ".$limit;
		$rows = $conn->query($sql);

		$result = $conn->query("SELECT FOUND_ROWS() AS count");
		$this->count = $result[0]['count'];

		return $rows;
	}

	public function insert($para)
	{
		$conn = $this->conn('master');
		
		$set = $this->parseSet($para);
	
		$sql = "INSERT INTO `".$this->getTablename()."` ".$set;
		$result = $conn->exec($sql);
		return $result?$conn->lastInsertId():false;
	}

	public function update($para)
	{
		$conn = $this->conn('master');
		
		$set = $this->parseSet($para);
	
		$where = $this->parseWhere($para);
	
		$sql = "UPDATE `".$this->getTablename()."` ".$set." ".$where;
		return $conn->exec($sql);
	}

	public function delete($para)
	{
		$conn = $this->conn('master');
		
		$where = $this->parseWhere($para);
	
		$sql = "DELETE FROM `".$this->getTablename()."` ".$where;
		return $conn->exec($sql);
	}

	private function parseSet($para)
	{
		$set = 'SET ';
		if(isset($para['set']))
		{
			foreach($para['set'] as $key=>$value)
			{
				$set .= $key."='".mysql_real_escape_string($value)."',";
			}
		}
		if(isset($para['set+']))
		{
			foreach($para['set+'] as $key=>$value)
			{
				$set .= $key."=".$value.",";
			}
		}
		$set = trim($set,',');
		return $set;
	}

	private function parseWhere($para)
	{
		$where = "";
		if(isset($para['where']))
		{
			$where = "WHERE ".$para['where']['where'];
			if(isset($para['where']['para'])){
				foreach($para['where']['para'] as $key=>$value)
				{
					if(is_array($value))
					{
						foreach($value as $key2=>$value2)
						{
							$value[$key2] = mysql_real_escape_string($value2);
						}
						$para['where']['para'][$key] = "'".implode("','",$value)."'";
					}
					else
					{
						$para['where']['para'][$key] = mysql_real_escape_string($value);
					}
				}
	
				$where = str_replace(array_keys($para['where']['para']),array_values($para['where']['para']),$where);
			}
		}
	
		return $where;
	}

	private function parseLimit($para)
	{
		$limit = "";
		if(isset($para['page']))
		{
			$para['page']['page'] = intval($para['page']['page']);
			$para['page']['size'] = intval($para['page']['size']);
			$limit = "LIMIT ".(($para['page']['page']-1)*$para['page']['size']).",".$para['page']['size'];
		}
		else if(isset($para['limit']))
		{
			$para['limit']['offset'] = intval($para['limit']['offset']);
			$para['limit']['limit'] = intval($para['limit']['limit']);
			$limit = "LIMIT ".$para['limit']['offset'].",".$para['limit']['limit'];
		}
	
		return $limit;
	}

	public function beginTransaction(Transaction $transaction)
	{
		$para = $this->cube->getMaster();
	
		$para['dbname'] = $this->dbname;
		if($this->cube->getDbSuffix())
		{
			$para['dbname'] .= '_'.$this->cube->getDbSuffix();
		}
	
		$db = new Db($para['host'],$para['port'],$para['user'],$para['pass'],$para['dbname']);
	
		$key = $para['user'].'@'.$para['host'].':'.$para['port'];
		$conn = $db->getConn();
		$transaction->beginTransaction($key,$conn);
	}
	
}

?>