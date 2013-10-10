define(['jquery', 'backbone', 'App'],
	function($, Backbone, App) {
		var FormModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + 'api/v2/forms'
			
		});
	
		return FormModel;
	});