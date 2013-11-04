<?php

class Db
{
	static private $conns;
	private $conn;
	
	public function __construct($host,$port,$user,$pass,$dbname)
	{
		if(!isset(Db::$conns[$host][$port][$user]))
		{
			try
			{
				$this->conn = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$dbname, $user, $pass);
				$this->conn->exec("SET NAMES utf8");
				Db::$conns[$host][$port][$user] = $this->conn;
				
				mysql_connect($host.':'.$port,$user,$pass);		
			}
			catch (PDOException $e)
			{
				throw new Exception_Default(401, '数据库连接失败');
			}			
		}
		else
		{
			$this->conn = Db::$conns[$host][$port][$user];
		}
	}

	public function query($sql)
	{
		$sth = $this->conn->query($sql);
		$result = $sth?$sth->fetchAll(PDO::FETCH_ASSOC):array();
		return $result;
	}

	public function exec($sql)
	{
		return $this->conn->exec($sql);
	}

	public function lastInsertId()
	{
		$result = $this->conn->lastInsertId();
		$result = $result?$result:true;
		return $result;
	}
	
	public function getConn()
	{
		return $this->conn;
	}

}

?>