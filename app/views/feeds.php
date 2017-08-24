<?php

	foreach($feeds as $feed)
	{
		echo '<article class="feed" data-fid="' . $feed->id . '">';
			echo '<h1><a class="js-pjax" href="/feeds/' . $feed->id . '">' . (strlen($feed->title) > 0 ? $feed->title : 'unknown feed') . '</a></h1>';
		echo '</article>';
	}
