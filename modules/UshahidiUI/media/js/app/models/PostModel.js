define(["jquery", "backbone"],
	function($, Backbone) {
		var PostModel = Backbone.Model.extend(
		{
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