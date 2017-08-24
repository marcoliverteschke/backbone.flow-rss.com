<?php

	class FeedHandler {

		private $redbean;
		private $simplepie;

		public function __construct($redbean, $simplepie) {
			$this->setRedBean($redbean);
			$this->setSimplepie($simplepie);
		}

		private function setRedbean($redbean) {
			$this->redbean = $redbean;
		}

		private function getRedbean() {
			return $this->redbean;
		}

		private function setSimplepie($simplepie) {
			$this->simplepie = $simplepie;
		}

		private function getSimplepie() {
			return $this->simplepie;
		}

		public function loadFeeds() {
			$feeds = $this->getRedbean()->getRedbean()->find('feed');
		}

		public function updateFeeds() {
			$feeds = $this->getRedbean()->getRedbean()->find('feed');

			foreach($feeds as $feed) {
				$this->updateFeed($feed);
			}
		}

		public function updateFeed($feed) {
			$this->getSimplepie()->enable_cache(false);
			$feed->last_fetch_started = time();
	  		$this->getRedbean()->getRedbean()->store($feed);
			$this->getSimplepie()->set_feed_url($feed->link);
			$this->getSimplepie()->init();

			if(strlen($this->getSimplepie()->get_title()) > 0) {
				$feed->title = $this->getSimplepie()->get_title();
			}

			$pietems = $this->getSimplepie()->get_items();

			if(is_array($pietems) && count($pietems)) {
				$feed->status = FLOW_FEED_STATUS_OK;
				foreach($pietems as $pietem)
				{
					$hash = md5($pietem->get_title() . '|||flow-rss.com|||' . $pietem->get_permalink());
					$exists = R::findOne('item', 'guid = ? OR hash = ?', array($pietem->get_id(true), $hash));
					if(count($exists) == 0)
					{
						$author = $pietem->get_author();
						$item = null;
						$item = R::dispense('item');
						$item->feed_id = $feed->id;
						if(!empty($author) && $author->get_name() != '')
							$item->author = $author->get_name();
						$item->description = $pietem->get_content();
						$item->guid = $pietem->get_id(true);
						$item->hash	= $hash;
						$item->link = $pietem->get_permalink();
						$item->pub_date = $pietem->get_date('U');
						if(empty($item->pub_date)) {
							$item->pub_date = time();
						}
						$item->title = $pietem->get_title();
						$item->added = time();
						R::store($item);
					}
				}
			} else {
				$feed->status = FLOW_FEED_STATUS_ERROR;
			}

			$feed->last_fetch_finished = time();
			$this->getRedbean()->getRedbean()->store($feed);
		}

		public function dedupeItems() {
			// clean up duplicates
			$dupes = $this->getRedbean()->getDatabaseAdapter()->get('SELECT guid, count(*) AS dupes FROM item GROUP BY guid HAVING dupes > 1');

			foreach($dupes as $dupe)
			{
				$items = $this->getRedbean()->getRedbean()->find('item', ' guid = ? ', array($dupe['guid']));
				$items_keys = array_keys($items);
				$keep = $items_keys[0];
				foreach($items as $key => $item)
				{
					if($key != $keep)
					{
						$this->getRedbean()->getRedbean()->trash($item);
					}
				}
			}
		}
	}