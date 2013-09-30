define(['jquery', 'backbone'],
	function($, Backbone) {
		// Creates a new Backbone Model class object
		var SearchFilterModel = Backbone.Model.extend(
		{
			// Default values for all of the Model attributes
			defaults :
			{
					'keywords': null,
					'locations': null,
					'category': null
			}
		});
	
		return SearchFilterModel;
	});