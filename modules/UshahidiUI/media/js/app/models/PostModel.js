define(['jquery', 'backbone'],
	function($, Backbone) {
		var PostModel = Backbone.Model.extend(
		{
			published : function ()
			{
				if (this.get('status') === 'published')
				{
					return true;
				}
			}
		});
	
		return PostModel;
	});