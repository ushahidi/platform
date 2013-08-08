define(["jquery", "backbone", "models/PostModel"],
	function($, Backbone, PostModel) {
		// Creates a new Backbone Collection class object
		var PostCollection = Backbone.Collection.extend(
		{
			// Tells the Backbone Collection that all of it's models will be of type Model (listed up top as a dependency)
			model : PostModel,
			// @todo move base url to Init config ?
			url: "/api/v2/posts",
			// The Ushahidi API returns models under "results".
			parse: function(response) {
				return response.results;
			}
		});
	
		return PostCollection;
	}); 