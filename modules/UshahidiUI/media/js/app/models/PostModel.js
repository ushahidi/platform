define(["jquery", "backbone", "App"],
	function($, Backbone, App) {
		var PostModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + "api/v2/posts",
			initialize : function() {
	
			},

			defaults :
			{
	
			},

			validate : function(attrs) {
	
			},
			
			published : function ()
			{
				if (this.get('status') == 'published') return true;
			}
		});
	
		return PostModel;
	}); 