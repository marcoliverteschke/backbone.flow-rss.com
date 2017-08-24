<?php

	require_once('../app/constants.php');
	require_once('vendor/autoload.php');
	require_once('../app/classes/FeedHandler.php');

	class_alias('\RedBeanPHP\R','\R');

	if(isset($argv[1]) && $argv[1] == 'production')
		R::setup('mysql:host=db602287155.db.1and1.com;dbname=db602287155', 'dbo602287155', 'ouD1ahM0oofuushi');
	else
		R::setup('mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;host=localhost;dbname=flowrss;port=8889', 'root', 'root');

	$feedHandler = new FeedHandler(R::getToolbox(), new SimplePie());
	$feedHandler->updateFeeds();

	R::close();