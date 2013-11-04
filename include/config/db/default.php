<?php

$deploy = Config::load('deploy');

return array(
	'master' => $deploy['mysql_1'],
	'slave' => array($deploy['mysql_2'],$deploy['mysql_3']),
	'dbSplit' => array(
		'' => array('min' => 1,'max' => 1000000,'master' => $deploy['mysql_1'],'slave' => array($deploy['mysql_2'],$deploy['mysql_3']),
			'tableSplit' => array(
				'' => array('min' => 1,'max' => 1000000,'master' => $deploy['mysql_1'],'slave' => array($deploy['mysql_2'],$deploy['mysql_3']),),
			),
		),
	),
);

?>