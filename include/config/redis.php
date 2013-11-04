<?php

$deploy = Config::load('deploy');

return array(
	'default' => $deploy['redis_1'],
	'version' => $deploy['redis_1'],
	'model_user' => $deploy['redis_1'],
);

?>