define(['jquery', 'backbone', 'models/TagModel', 'App'],
	function($, Backbone, TagModel, App) {
		// Creates a new Backbone Collection class object
		var TagCollection = Backbone.Collection.extend(
		{
			model : TagModel,
			url: App.config.baseurl + 'api/v2/tags',
			// The Ushahidi API returns models under 'results'.
			parse: function(response) {
				return response.results;
			}
		});
	
		return TagCollection;
	});