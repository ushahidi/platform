define(['jquery', 'backbone', 'models/FormModel', 'App'],
	function($, Backbone, FormModel, App)
	{
		// Creates a new Backbone Collection class object
		var FormCollection = Backbone.Collection.extend(
		{
			model : FormModel,
			url: App.config.baseurl + 'api/v2/forms',
			// The Ushahidi API returns models under 'results'.
			parse: function(response)
			{
				return response.results;
			}
		});
	
		return FormCollection;
	});