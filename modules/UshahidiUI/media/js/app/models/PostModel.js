define(['jquery', 'backbone', 'App', 'underscore', 'models/UserModel'],
	function($, Backbone, App, _, UserModel) {
		var PostModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + 'api/v2/posts',
			
			published : function ()
			{
				if (this.get('status') === 'published')
				{
					return true;
				}
			},

			tags : function ()
			{
				return _.map(this.get('tags'), function(tag)
				{
					var tagModel = App.Collections.Tags.get(tag.id);
					return tagModel ? tagModel.toJSON() : null;
				});
			},

			user : function ()
			{
				var user = new UserModel();
				if (typeof this.get('user') !== 'undefined')
				{
					user.set(this.get('user'));
					user.fetch();
				}
				return user;
			}
		});

		return PostModel;
	});