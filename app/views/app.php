<div class="container--app_container">
	<div class="container--global_actions">
		<a class="link link--action_menu" title="Toggle Menu"></a>		
		<a class="link link--action_mark_all_read" title="Mark all as read"></a>		
		<a class="link link--action_previous_item" title="Previous item"></a>		
		<a class="link link--action_next_item" title="Next item"></a>		
	</div>
	<div class="container--feeds_container">
		<div class="container--feeds_actions">
			<a class="link link--action_subscribe" title="Subscribe to feed"></a>		
			<a class="link link--action_edit_feed disabled" title="Edit feed"></a>		
			<a class="link link--action_unsubscribe disabled" title="Delete feed"></a>		
		</div>
	</div>
	<div class="container--items_container"></div>	
	<div class="container--subscription_form">
		<form action="/feeds/new" method="POST">
			<input type="url" name="feed_url" placeholder="Paste feed URL here" />
			<input type="submit" value="Subscribe" />
		</form>
	</div>
	<div class="container--edit_form">
		<form action="/feeds/update" method="POST">
			<input type="url" name="feed_url" placeholder="Paste feed URL here" />
			<input type="hidden" name="feed_id" placeholder="Paste feed URL here" />
			<input type="submit" value="Update" />
		</form>
	</div>
	<div class="container--progress_container"><span class="indicator--progress_bar"></span></div>
</div>
<script type="x-template/underscore" class="template--feed_item">
	<h1><%= feed.title %><span class="bubble--unread_count"><%= feed.unread_count %></span></h1>
</script>
<script type="x-template/underscore" class="template--item_item">
	<h1><%= item.title %></h1>
	<div class="container--item_content"><%= item.description %></div>
	<div class="bar--item_meta_bar">
		<span><% print(moment(item.pub_date * 1000).format("DD.MM.YYYY")); %></span>
		<span><a class="link link--action_star" href="javascript:void(0)" title="Star / unstar item"></a></span>
		<span><a class="link link--action_original" href="<%= item.link %>" target="_blank" title="Go to original"></a></span>
		<span><a class="link link--action_instapaper" href="http://www.instapaper.com/edit?url=<% print(encodeURIComponent(item.link)); %>&title=<% print(encodeURIComponent(item.title)); %>" target="_blank" title="Send to Instapaper"></a></span>
		<span><a class="link link--feed" href="#feeds/<%= item.feed_id %>/items"><%= item.feed_title %></a></span>
	</div>
</script>
<script type="x-template/underscore" class="template--no_items">
	<h1 class="there-is-nothing-here">Nothing to see here. Why don't you pick a feed, sailor?</h1>
</script>
<script src="/js/app.js"></script>
