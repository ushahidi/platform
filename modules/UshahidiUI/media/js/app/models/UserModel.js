define(['backbone', 'App'],
	function(Backbone, App) {
		var UserModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + 'api/v2/users'
		});
		return UserModel;
	});