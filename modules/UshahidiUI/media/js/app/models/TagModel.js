define(['jquery', 'backbone', 'App'],
	function($, Backbone, App) {
		var TagModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + 'api/v2/tags'
		});
	
		return TagModel;
	});