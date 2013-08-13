define(["jquery", "backbone", "models/PostModel", "App"],
	function($, Backbone, PostModel, App) {
		// Creates a new Backbone Collection class object
		var PostCollection = Backbone.Collection.extend(
		{
			model : PostModel,
			url: App.config.baseurl + "api/v2/posts",
			// The Ushahidi API returns models under "results".
			parse: function(response) {
				return response.results;
			}
		});
	
		return PostCollection;
	}); 