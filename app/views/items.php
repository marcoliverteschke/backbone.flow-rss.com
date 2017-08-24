<?php

	if(isset($feed))
	{
		echo '<header><h1>Items for „' . $feed->title . '” <a class="pictogram" data-tool="unsubscribe" data-fid="' . $feed->id . '" href="javascript:void(0);">I</a></h1></header>';
	}

	foreach($items as $item)
	{
		echo '<article class="item ' . ($item->time_starred > 0 ? ' starred' : '') . ($item->time_read > 0 ? ' read' : '') . '" data-guid="' . $item->guid . '">';
			echo '<h1>' . $item->title . '</h1>';
			echo '<footer>';
				echo date('d.m.Y H:i', $item->pub_date);
				echo ' | ';
				echo '<a class="js-pjax" href="/feeds/' . $item->feed->id . '">' . $item->feed->title . '</a>';
				echo ' | ';
				echo '<a class="pictogram" href="' . $item->link . '" target="_blank" title="Open original article">o</a>';
				echo ' | ';
				echo '<a class="pictogram" data-tool="star" href="javascript:void(0);" title="Star item">*</a>';
				echo ' | ';
				echo '<a class="pictogram" data-tool="instapaper" href="http://www.instapaper.com/edit?url=' . urlencode($item->link) . '&title=' . urlencode($item->title) . '" title="Send article to Instapaper" target="_blank">a</a>';
			echo '</footer>';
		echo '</article>';
	}
	
	if(count($items) == 0)
		echo '<section class="backdrop space"><h1>A vast emptiness spreads before you…</h1></section>';
