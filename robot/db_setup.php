<?php

	$config_file_path = '../config/config.yml';
	if(file_exists($config_file_path) && is_file($config_file_path) && is_readable($config_file_path)) {
		$config = Yaml::parseFile($config_file_path);
	} else {
		throw new Exception('Could not read config file');
	}

	R::setup(
		sprintf(
			'mysql:%shost=%s;dbname=%s',
			!empty($config['database']['unix_socket']) ? $config['database']['unix_socket'] . ';' : '',
			$config['database']['host'],
			$config['database']['database'],
			!empty($config['database']['port']) ? ';port=' . $config['database']['port'] : ''
		), 
		$config['database']['user'], 
		$config['database']['password']
	);

	R::freeze(true);