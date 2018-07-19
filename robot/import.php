<?php

	require_once('vendor/autoload.php');

	class_alias('\RedBeanPHP\R','\R');
	class_alias('\Symfony\Component\Yaml\Yaml','\Yaml');
	
	var_dump($argv);

	require_once('db_setup.php');
	
	$handle = @fopen('./subscriptions.xml', 'r');
	if($handle)
	{
		$file_content = '';
		
		while($line = @fread($handle, 1024))
		{
			$file_content .= $line;
		}
		@fclose($handle);
		
		if(strlen($file_content) > 0)
		{
			$xml = new SimpleXMLElement($file_content);
			
			foreach($xml->body->outline as $outline)
			{
				if($outline['type'] == 'rss')
				{
					store_feed($outline);
				} else {
					foreach($outline->outline as $outline2)
					{
						store_feed($outline2);
					}
				}
			}
		}
	}
	R::close();
	


	function store_feed($outline)
	{
		$feed = R::dispense('feed');
		$feed->title = (string)$outline['title'];
		$feed->link = (string)$outline['xmlUrl'];
		print_r($feed);
		R::store($feed);
	}