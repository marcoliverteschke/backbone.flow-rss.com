<?php

	require_once('../app/constants.php');
	require_once('vendor/autoload.php');
	require_once('../app/classes/FeedHandler.php');

	class_alias('\RedBeanPHP\R','\R');
	class_alias('\Symfony\Component\Yaml\Yaml','\Yaml');

	require_once('db_setup.php');

	$feedHandler = new FeedHandler(R::getToolbox(), new SimplePie());
	$feedHandler->dedupeItems();

	R::close();