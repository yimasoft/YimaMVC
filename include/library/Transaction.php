<?php

class Transaction
{
	private $servers = array();

	public function beginTransaction($key,PDO $conn)
	{
		if(!isset($this->servers[$key]))
		{
			$this->servers[$key] = $conn;
			$this->servers[$key]->beginTransaction();
		}
	}

	public function commit()
	{
		foreach($this->servers as $conn)
		{
			$conn->commit();
		}
	}

	public function rollBack()
	{
		foreach($this->servers as $conn)
		{
			$conn->rollBack();
		}
	}

}

?>