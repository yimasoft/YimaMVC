<?php

$deploy = Config::load('deploy');

return array(
	'default'	=> array($deploy['mem_1'],$deploy['mem_1']),
);

?>