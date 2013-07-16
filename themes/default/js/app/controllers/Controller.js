define(['App', 'backbone', 'marionette', 'views/WelcomeView', 'views/HeaderView', 'views/PostListView', 'collections/PostCollection'],
	function(App, Backbone, Marionette, WelcomeView, HeaderView, PostListView, PostCollection) {
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function(options) {
				App.headerRegion.show(new HeaderView());
			},
			//gets mapped to in AppRouter's appRoutes
			index : function() {
				App.mainRegion.show(new WelcomeView());
			},
			postList : function() {
				App.Posts = new PostCollection();
				App.Posts.fetch();
				
				App.mainRegion.show(new PostListView({
					collection: App.Posts
				}));
			}
		});
	}); 