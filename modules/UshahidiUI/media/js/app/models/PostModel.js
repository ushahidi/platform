define(["jquery", "backbone", "App"],
	function($, Backbone, App) {
		var PostModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + "api/v2/posts",
			
			published : function ()
			{
				if (this.get('status') === 'published')
				{
					return true;
				}
			},
			
			tags : function () {
				return _.map(this.get('tags'), function(tag)
				{
					var tagModel = App.Collections.Tags.get(tag.id);
					return tagModel ? tagModel.toJSON() : null;
				});
			}
		});
	
		return PostModel;
	});