<?php

$config['controller'] = 'home';

$config['adapters'] = array(
	'paginator' => array(
		'custom'
		),
	'session' => array(
		//'cookie',
		'database'
		)
	);

$config['modules'] = array(
	'crypt',
	//'storage',
	'upload',
	//'form',
	'hook',
	//'url',
	'database',
	'debug',
	);

$config['libraries'] = array(
	);

$config['helpers'] = array(
	);

$config['models'] = array(
	);

?>
