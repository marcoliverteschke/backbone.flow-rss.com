/**
 * @TODO
 * -update new items feed entry on read-
 * -update new items feed entry on ANY read-
 * -update starred items feed entry on star/unstar-
 * -regain current feed model on reload-
 * update unread count on feed when item is read on new or starred view
 */

var Feed = Backbone.Model.extend();
var Item = Backbone.Model.extend();
var NoItemsTemplate = _.template($('.template--no_items').html());

var currentHashBang = '';
var currentFeedModel = null;
var unreadItemsFeedModel = null;
var starredItemsFeedModel = null;
var isProbablyMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

var A_KEY = 65;
var J_KEY = 74;
var K_KEY = 75;
var S_KEY = 83;

$('body').on('keyup', function(event){
	switch(parseInt(event.which)) {
		case A_KEY:
			if(event.shiftKey) {
				mark_all_items_read();
			}
			break;
		case J_KEY:
			select_next_item();
			break;
		case K_KEY:
			select_previous_item();
			break;
		case S_KEY:
			if($('.item--item_active').hasClass('item--item_starred')) {
				var _this = $('.item--item_active');
				$.post('/items/' + _this.data('id') + '/unstar', function(){
					_this.removeClass('item--item_starred');
					starredItemsFeedModel.fetch();
				});
			} else {
				var now = moment().format('X');
				var _this = $('.item--item_active');
				$.post('/items/' + _this.data('id') + '/star/' + now, function(){
					_this.addClass('item--item_starred');
					starredItemsFeedModel.fetch();
				});
			}
			break;
	}
});

$('.link--action_mark_all_read').on('click', function(){
	mark_all_items_read();
});

$('.link--action_previous_item').on('click', function(){
	select_previous_item();
});

$('.link--action_next_item').on('click', function(){
	select_next_item();
});

$('.link--action_subscribe').on('click', function(){
	$('.container--subscription_form').toggleClass('container--subscription_form--visible');
});

$('.container--subscription_form form').on('submit', function(){
	$.post($(this).attr('action'), {feed_url : $(this).find('[name=feed_url]').val()}, function(data){
		if(typeof data.status !== 'undefined' && data.status == 'success') {
			window.location.reload(false);
		}
	}, 'json');
	return false;
});

$('.link--action_edit_feed').on('click', function(){
	if($(this).hasClass('disabled')) {
		return false;
	}
	$('.container--edit_form').find('[name=feed_url]').val(currentFeedModel.attributes.link);
	$('.container--edit_form').find('[name=feed_id]').val(currentFeedModel.attributes.id);
	$('.container--edit_form').toggleClass('container--edit_form--visible');
});

$('.container--edit_form form').on('submit', function(){
	setProgressBar(5);
	$.post($(this).attr('action'), {feed_id : $(this).find('[name=feed_id]').val(), feed_url : $(this).find('[name=feed_url]').val()}, function(data){
		if(typeof data.status !== 'undefined' && data.status == 'success') {
			setProgressBar(100);
			window.location.reload(false);
		}
	}, 'json');
	return false;
});

$('.link--action_unsubscribe').on('click', function(){
	if($(this).hasClass('disabled')) {
		return false;
	}
	$.post('/feeds/delete', { feed_id : currentFeedModel.attributes.id }, function(data){
		$('.item--feed_item--active').fadeOut(function(){
			$(this).remove();
		});
	}, 'json');
});

function setProgressBar(value) {
	if(typeof value == 'undefined') {
		value = 0;
	}
	value = parseInt(value);
	if(value > 100) {
		value = 100;
	}
	if(value === 0 || value === 100) {
		window.setTimeout(function(){
			$('.container--progress_container').hide();	
		}, 2000);
	} else {
		$('.container--progress_container').show();	
	}
	$('.container--progress_container span').css({width: value + '%'});
}

function select_next_item() {
	if($('.item--item_active').length === 0) {
		$('.item--item_item:first h1').trigger('click');
	} else {
		if($('.item--item_active').next('.item--item_item').length === 1) {
			$('.item--item_active').next('.item--item_item').find('h1').trigger('click');
		}
	}
}

function select_previous_item() {
	if($('.item--item_active').length === 0) {
		$('.item--item_item:last h1').trigger('click');
	} else {
		if($('.item--item_active').prev('.item--item_item').length === 1) {
			$('.item--item_active').prev('.item--item_item').find('h1').trigger('click');
		}
	}
}

function mark_all_items_read() {
	var ids_to_mark_as_read = [];
	setProgressBar(5);
	$('.item--item_item:not(.item--item_read)').each(function(i, e){
		ids_to_mark_as_read[i] = $(e).data('id');
	});
	var now = moment().format('X');
	$.post('/items/read/' + now, {'ids': ids_to_mark_as_read}, function(){
		$('.item--item_item:not(.item--item_read)').addClass('item--item_read');
		currentFeedModel.fetch();
		unreadItemsFeedModel.fetch();
		setProgressBar(100);
	});
}

function mark_item_as_read(_this) {
	if(parseInt(_this.model.attributes.time_read) === 0) {
		var now = moment().format('X');
		_this.model.set('time_read', now);
		$.post('/items/' + _this.$el.data('id') + '/read/' + now, function(){
			_this.$el.addClass('item--item_read');
			currentFeedModel.fetch();
			unreadItemsFeedModel.fetch();
			_.each(feeds.models, function(element, index, list){
				if(element.id === _this.model.attributes.feed_id) {
					element.fetch();
				}
			});
		});
	}	
}

function reveal_item_contents(_this) {
	_this.$el.find('.container--item_content').slideDown(function(){
		$('html,body').scrollTop($('.item--item_active').position().top);
	});
}

function fetch_item_data(_this, fetch_only) {
	if(typeof _this.model.attributes.description == 'undefined') {
		$.get('/items/' + _this.$el.data('id'), function(data){
			_this.model.set('description', data.description);
			if(!fetch_only) {
				mark_item_as_read(_this);
				reveal_item_contents(_this);
			}
		}, 'json');
	} else {
		if(!fetch_only) {
			mark_item_as_read(_this);
			reveal_item_contents(_this);
		}
	}
}

function load_item_data(_this) {
	setProgressBar(5);
	_this.$el.addClass('item--item_active');
	fetch_item_data(_this, false);
	if(_this.$el.prev('.item--item_item').length === 1) {
		var _prev = _this.$el.prev('.item--item_item');
		_prev.trigger('prefetch');
	}
	if(_this.$el.next('.item--item_item').length === 1) {
		var _next = _this.$el.next('.item--item_item');
		_next.trigger('prefetch');
	}
	setProgressBar(100);
}

$('.link--action_menu').on('click', function(){
	$('body').toggleClass('menu-visible');
});

var itemsFetchOptions = {
	success: function(collection, response, options){
		$('.container--items_container').empty();
		for(item_key in response) {
			var item_item = response[item_key];
			var item_view = new ItemView({
				model: new Item(item_item),
			});
			item_view.render();
			$('.container--items_container').append(item_view.el);
			$('.container--items_container').scrollTop(0);
		}
		if(response.length === 0) {
			$('.container--items_container').append(NoItemsTemplate());
		}
		if(!isProbablyMobile) {
			$('.container--items_container').find('.item--item_item:not(.item--item_read),.item--item_starred').each(function(i, e){
				window.setTimeout(function(){
					$(e).trigger('prefetch');
				}, (1000 * i));
			});
			$('.container--items_container').find('.item--item_item').each(function(i, e){
				$(e).hover(function(){
					$(e).trigger('prefetch');
				}, function(){});
			});
		}
		setProgressBar(100);
	},
	error:  function(collection, response, options){
		alert('Items not loaded');
	},
};
var FlowRouter = Backbone.Router.extend({
	routes: {
		"items/new"            : "items_new",
		"feeds/:feed_id/items" : "items_in_feed",
		"items/starred"        : "items_starred"
	},
	items_new: function(feed_id){
		var itemsList = new Backbone.Collection;
		itemsList.url = 'items/new';

		setProgressBar(5);
		itemsList.fetch(itemsFetchOptions);
	},
	items_starred: function(feed_id){
		var itemsList = new Backbone.Collection;
		itemsList.url = 'items/starred';

		setProgressBar(5);
		itemsList.fetch(itemsFetchOptions);
	},
	items_in_feed: function(feed_id){
		var itemsList = new Backbone.Collection;
		itemsList.url = 'feeds/' + feed_id + '/items';

		setProgressBar(5);
		itemsList.fetch(itemsFetchOptions);
	}
});

var FeedListView = Backbone.View.extend({
	el: '.container--feeds_container',
	collection: {},
	render: function(){
		this.collection.each(function(feed_item){
			var feed_item_view = new FeedItemView({
				model: feed_item,
			});
			feed_item_view.render();
			feedListView.$el.append(feed_item_view.el);
		});
		if(typeof currentFeedModel !== 'undefined' && currentFeedModel !== null && currentFeedModel.attributes.id.search(/^[0-9]+$/) === 0) {
			$('.container--feeds_actions .link--action_edit_feed,.link--action_unsubscribe').removeClass('disabled');
		} else {
			$('.container--feeds_actions .link--action_edit_feed,.link--action_unsubscribe').addClass('disabled');
		}
	},
});

var FeedItemView = Backbone.View.extend({
	template: _.template($('.template--feed_item').html()),
	tagName: 'section',
	attributes: function(){
		return {
			'class': 'item item--feed_item ' + (parseInt(this.model.attributes.status) !== 0 ? "item--feed_item--status_error " : "") + (currentHashBang == this.model.attributes.url ? 'item--feed_item--active' : ''),
		};
	},
	initialize: function(){
		this.listenTo(this.model, "change", this.render);
	},
	render: function(){
		this.$el.html(this.template({feed: this.model.attributes}));
		return this;
	},
	events: {
		"click": "toggle",
	},
	toggle: function(){
		setProgressBar(0);
		$(this.el).parent().find('.item--feed_item--active').removeClass('item--feed_item--active');
		$(this.el).addClass('item--feed_item--active');
		if(this.model.attributes.id.search(/^[0-9]+$/) === 0) {
			$('.container--feeds_actions .link--action_edit_feed,.link--action_unsubscribe').removeClass('disabled');
		} else {
			$('.container--feeds_actions .link--action_edit_feed,.link--action_unsubscribe').addClass('disabled');
		}
		this.model.fetch();
		currentHashBang = this.model.attributes.url;
		currentFeedModel = this.model;
		app.navigate(this.model.attributes.url, {trigger: true});
		$('body.menu-visible').removeClass('menu-visible');
	},
});

var ItemView = Backbone.View.extend({
	template: _.template($('.template--item_item').html()),
	tagName: 'section',
	attributes: function(){
		return {
			'class': 'item item--item_item ' + (parseInt(this.model.attributes.time_starred) !== 0 ? "item--item_starred " : "") + (parseInt(this.model.attributes.time_read) !== 0 ? "item--item_read " : ""),
			'data-id': this.model.id,
		};
	},
	initialize: function(){
		this.listenTo(this.model, "change", this.render);
	},
	render: function(){
		this.$el.html(this.template({item: this.model.attributes}));
		this.$el.find('.container--item_content a').attr('target', '_blank');
		return this;
	},
	events: {
		"click h1": "toggle",
		"click .link--action_star": "star_item",
		"prefetch": "prefetch"
	},
	toggle: function(event){
		var _this = this;
		if(!_this.$el.hasClass('item--item_active')) {
			if($('.item--item_active').length === 1) {
				$('.item--item_active').find('.container--item_content').slideUp(function(){
					$('.item--item_active').removeClass('item--item_active');
					load_item_data(_this);
				});
			} else {
				load_item_data(_this);
			}
		} else {
			$('.item--item_active').find('.container--item_content').slideUp(function(){
				$('.item--item_active').removeClass('item--item_active');
			});
		}
	},
	star_item: function(event){
		if(parseInt(this.model.attributes.time_starred) === 0) {
			var now = moment().format('X');
			// this.model.set('time_starred', now);
			var _this = this;
			$.post('/items/' + _this.$el.data('id') + '/star/' + now, function(){
				_this.$el.addClass('item--item_starred');
				starredItemsFeedModel.fetch();
			});
		} else {
			// this.model.set('time_starred', 0);
			var _this = this;
			$.post('/items/' + _this.$el.data('id') + '/unstar', function(){
				_this.$el.removeClass('item--item_starred');
				starredItemsFeedModel.fetch();
			});
		}
	},
	prefetch: function(event) {
		var _this = this;
		fetch_item_data(_this, true);
	}
});


var FeedList = Backbone.Collection.extend({
	url: '/feeds',
	model: Feed,
});
var feeds = new FeedList;

var feedListView = new FeedListView({collection: feeds});

// populate feeds list with entries for feeds, all items, starred items
var feedsFetchOptions = {
	success: function(collection, response, options){
		_.each(feeds.models, function(element, index, list){
			if(element.attributes.id == 'unread_items') {
				unreadItemsFeedModel = element;
			}
			if(element.attributes.id == 'starred_items') {
				starredItemsFeedModel = element;
			}
			if(typeof element.attributes.url === 'undefined' && typeof element.attributes.id !== 'undefined') {
				element.attributes.url = 'feeds/' + element.attributes.id + '/items';
			}
			if(currentHashBang == element.attributes.url) {
				currentFeedModel = element;
			}
		});
		feedListView.render();
	},
	error:  function(collection, response, options){
		console.error('Feeds not loaded');
	},	
};
feeds.fetch(feedsFetchOptions);

// on clock on feed item, load items into right side pane

var app = new FlowRouter;
app.on('route', function(route, params){
	currentHashBang = window.location.hash.length > 1 ? window.location.hash.substring(1) : '';
	_.each(feeds.models, function(element, index, list){
		if(element.attributes.url === currentHashBang) {
			currentFeedModel = element;
		}
	});
});

Backbone.history.start()
$('.container--items_container').append(NoItemsTemplate());