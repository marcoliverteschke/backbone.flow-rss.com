<?php

	require_once('../config/config.php');
	require_once('constants.php');
	require_once('classes/FeedHandler.php');
	require_once('vendor/autoload.php');
	class_alias('\RedBeanPHP\R','\R');

	Flight::before('start', function(&$params, &$output){
		global $config;
		$lessc = new lessc;
		$lessc->ccompile('css/styles.less', 'css/styles.css');
		$request = Flight::request();

		if($request->url == '/login' && $request->method == 'POST')
		{
			if(isset($request->data['username']) && $request->data['username'] == 'marcoliver' && isset($request->data['password']) && md5($request->data['password']) == $config['password_hash'])
			{
				setcookie('flow_authenticated', 'true', time()+60*60*24*30, '/');
				Flight::redirect('/#items/new');
			}
		}

		if($request->url != '/login')
		{
			if(isset($request->cookies['flow_authenticated']) && $request->cookies['flow_authenticated'] == 'true')
			{
				Flight::set('authenticated', true);
			} else {
				Flight::set('authenticated', false);
				Flight::redirect('/login');
			}
		}

		R::setup(
			sprintf(
				'mysql:%shost=%s;dbname=%s',
				isset($config['db']['unix_socket']) && is_string($config['db']['unix_socket']) ? $config['db']['unix_socket'] . ';' : '',
				$config['db']['host'],
				$config['db']['database'],
				isset($config['db']['port']) && is_string($config['db']['port']) ? ';port=' . $config['db']['port'] : ''
			), 
			$config['db']['user'], 
			$config['db']['password']
		);

		// if($_SERVER['SERVER_NAME'] == 'flow-rss.com')
		// {
		// 	R::setup('mysql:host=db602287155.db.1and1.com;dbname=db602287155', 'dbo602287155', 'ouD1ahM0oofuushi');
		// } else {
		// 	R::setup('mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;host=localhost;dbname=flowrss;port=8889', 'root', 'root');
		// }
		R::freeze(true);

		$feedHandler = new FeedHandler(R::getToolbox(), new SimplePie());
		Flight::set('feedHandler', $feedHandler);
	});

	Flight::after('start', function(&$params, &$output){
		R::close();
	});

	/*
		/ => R::items/new
		feeds => all feeds
		feeds/@id => single feed, items scrollfetched
		items => R::items/new
		items/new => unread items chronological
		items/marked => marked items
	*/
	Flight::route('/login', function(){
		Flight::render('login', null, 'body_content');
		Flight::render('layout');
	});

	Flight::route('/logout', function(){
		setcookie('flow_authenticated', null, 0, '/');
		Flight::set('authenticated', false);
		Flight::redirect('login');
	});

	Flight::route('POST /items/read/@timestamp', function($timestamp) {
		$request = Flight::request();
		if(is_array($request->data['ids']) && count($request->data['ids']) > 0) {
			R::exec('UPDATE item i SET i.time_read = :timestamp WHERE i.id IN (' . implode(',', $request->data['ids']) . ')', array(':timestamp' => $timestamp));
		}
		Flight::render('json', array('content' => ''), 'body_content');
		Flight::render('blank');
	});

	Flight::route('POST /items/@item_id/star/@timestamp', function($item_id, $timestamp) {
		R::exec('UPDATE item i SET i.time_starred = :timestamp WHERE i.id = :item_id', array(':item_id' => $item_id, ':timestamp' => $timestamp));
		Flight::render('json', array('content' => ''), 'body_content');
		Flight::render('blank');
	});

	Flight::route('POST /items/@item_id/unstar', function($item_id) {
		R::exec('UPDATE item i SET i.time_starred = 0 WHERE i.id = :item_id', array(':item_id' => $item_id));
		Flight::render('json', array('content' => ''), 'body_content');
		Flight::render('blank');
	});

	Flight::route('POST /items/@item_id/read/@timestamp', function($item_id, $timestamp) {
		R::exec('UPDATE item i SET i.time_read = :timestamp WHERE i.id = :item_id', array(':item_id' => $item_id, ':timestamp' => $timestamp));
		Flight::render('json', array('content' => ''), 'body_content');
		Flight::render('blank');
	});

	Flight::route('GET /items/new', function(){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$items = R::getAll('SELECT id, title, pub_date, link, time_read, time_starred, feed_id, (SELECT title FROM feed WHERE id = item.feed_id) AS feed_title FROM item WHERE time_read = 0 ORDER BY pub_date ASC LIMIT 500 ', array());
		Flight::render('json', array('content' => $items), 'body_content');
		Flight::render('blank');
	});

	Flight::route('GET /items/starred', function(){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$items = R::getAll('SELECT id, title, pub_date, link, time_read, time_starred, (SELECT title FROM feed WHERE id = item.feed_id) as feed_title, feed_id FROM item WHERE time_starred != 0 ORDER BY pub_date DESC LIMIT 500 ', array());
		Flight::render('json', array('content' => $items), 'body_content');
		Flight::render('blank');
	});

	Flight::route('GET /items/@item_id', function($item_id){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$item = R::getRow('SELECT *, (SELECT title FROM feed WHERE id = item.feed_id) as feed_title FROM item WHERE id = :item_id ', array(':item_id' => $item_id));
		Flight::lastModified($item['added']);
		Flight::render('json', array('content' => $item), 'body_content');
		Flight::render('blank');
	});

	Flight::route('GET /feeds/@feed_id/items', function($feed_id){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$items = R::getAll('SELECT id, title, pub_date, link, time_read, time_starred, (SELECT title FROM feed WHERE id = :feed_id) as feed_title, feed_id FROM item WHERE feed_id = :feed_id ORDER BY time_starred DESC, pub_date DESC LIMIT 250 ', array(':feed_id' => $feed_id));
		Flight::render('json', array('content' => $items), 'body_content');
		Flight::render('blank');
	});

	Flight::route('POST /feeds/delete', function(){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$status = 0;
		if(isset($request->data['feed_id']) && preg_match("/^[0-9]+$/", $request->data['feed_id'])) {
			$items = R::find('item', ' feed_id = :feed_id', [':feed_id' => $request->data['feed_id']]);
			$feed = R::load('feed', $request->data['feed_id']);

			R::trashAll($items);
			R::trash($feed);

			$items = R::count('item', ' feed_id = :feed_id', [':feed_id' => $request->data['feed_id']]);
			$feed = R::count('feed', ' id = :feed_id ', [':feed_id' => $request->data['feed_id']]);

			if($items === 0 && $feed === 0) {
				$status = 1;
			}
		}
		Flight::render('json', array('content' => ['status' => $status]), 'body_content');
		Flight::render('blank');
	});

	Flight::route('GET /feeds/@feed_id', function($feed_id){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$feed = [];
		if(preg_match("/^[0-9]+$/", $feed_id)) {
			$feed = R::getRow('SELECT f.*, (SELECT COUNT(*) FROM item i WHERE i.feed_id = f.id AND time_read = 0) as unread_count FROM feed f WHERE f.id = :feed_id ', array(':feed_id' => $feed_id));
		} else {
			switch($feed_id) {
				case 'unread_items':
					$unread_total = R::count('item', ' time_read = 0 ');
					$feed = array('id' => 'unread_items', 'url' => 'items/new', 'title' => 'unread items', 'unread_count' => $unread_total, 'status' => 0);
					break;
				case 'starred_items':
					$starred_total = R::count('item', ' time_starred != 0 ');
					$feed = array('id' => 'starred_items', 'url' => 'items/starred', 'title' => 'starred items', 'unread_count' => $starred_total, 'status' => 0);
					break;
			}
		}
		Flight::render('json', array('content' => $feed), 'body_content');
		Flight::render('blank');
	});

	Flight::route('POST /feeds/new', function(){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$feed = R::dispense('feed');
		$feed->link = $request->data['feed_url'];
		R::store($feed);
		$feedHandler = Flight::get('feedHandler');
		$feedHandler->updateFeed($feed);
		Flight::render('json', array('content' => ['status' => 'success']), 'body_content');
		Flight::render('blank');
	});

	Flight::route('POST /feeds/update', function(){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$status = 'error';
		if(isset($request->data['feed_id']) && isset($request->data['feed_url'])) {
			$feed = R::load('feed', $request->data['feed_id']);
			$feed->link = $request->data['feed_url'];
			R::store($feed);
			$feedHandler = Flight::get('feedHandler');
			$feedHandler->updateFeed($feed);
			$status = 'success';
		}
		Flight::render('json', array('content' => ['status' => $status]), 'body_content');
		Flight::render('blank');
	});

	Flight::route('GET /feeds', function(){
		$request = Flight::request();
		if(!$request->ajax) {
			Flight::redirect('/');
		}
		$feeds = R::getAll('SELECT f.*, (SELECT COUNT(*) FROM item i WHERE i.feed_id = f.id AND time_read = 0) as unread_count FROM feed f ORDER BY unread_count DESC, f.title ASC');
		$unread_total = R::count('item', ' time_read = 0 ');
		$starred_total = R::count('item', ' time_starred != 0 ');
		array_unshift($feeds, array('id' => 'unread_items', 'url' => 'items/new', 'title' => 'unread items', 'unread_count' => $unread_total, 'status' => 0), array('id' => 'starred_items', 'url' => 'items/starred', 'title' => 'starred items', 'unread_count' => $starred_total, 'status' => 0));
		Flight::render('json', array('content' => $feeds), 'body_content');
		Flight::render('blank');
	});

	Flight::route('*', function(){
		Flight::render('app', null, 'body_content');
		Flight::render('layout');
	});

	Flight::start();

?>
