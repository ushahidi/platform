define(["jquery", "backbone"],
	function($, Backbone) {
		// Creates a new Backbone Model class object
		var SearchFilterModel = Backbone.Model.extend(
		{
			initialize : function() {
	
			},
	
			// Default values for all of the Model attributes
			defaults :
			{
					"keywords": null,
					"locations": null,
					"category": null,	
			},
	
			// Get's called automatically by Backbone when the set and/or save methods are called (Add your own logic)
			validate : function(attrs) {
	
			}
		});
	
		return SearchFilterModel;
	});