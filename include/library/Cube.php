<?php

class Cube
{
	private $group = 'default';
	private $master;
	private $slave;
	private $dbSuffix='';
	private $tableSuffix='';

	public function __construct($group='default')
	{
		$this->group = $group;
	}

	public function turn($id=1)
	{
		$config = Config::load('db/'.$this->group);
		
		$this->master = $config['master'];
		$this->slave = $config['slave'];
		if(isset($config['dbSplit']))
		{
			foreach($config['dbSplit'] as $dbSuffix=>$value)
			{
				if($id>=$value['min'] && $id<=$value['max'])
				{
					$this->master = $value['master'];
					$this->slave = $value['slave'];
					$this->dbSuffix = $dbSuffix;
					if(isset($value['tableSplit']))
					{
						foreach($value['tableSplit'] as $tableSuffix=>$value2)
						{
							if($id>=$value2['min'] && $id<=$value2['max'])
							{
								$this->master = $value2['master'];
								$this->slave = $value2['slave'];
								$this->tableSuffix = $tableSuffix;
								break;
							}
						}
					}
					break;
				}
			}
		}
	}

	public function getMaster()
	{
		return $this->master;
	}

	public function getSlave()
	{
		shuffle($this->slave);
		return $this->slave[0];
	}

	public function getDbSuffix()
	{
		return $this->dbSuffix;
	}

	public function getTableSuffix()
	{
		return $this->tableSuffix;
	}

}

?>