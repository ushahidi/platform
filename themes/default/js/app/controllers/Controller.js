define(['App', 'backbone', 'marionette', 'views/AppLayout', 'views/HomeLayout', 'views/HeaderView', 'views/PostListView', 'collections/PostCollection'],
	function(App, Backbone, Marionette, AppLayout, HomeLayout, HeaderView, PostListView, PostCollection) {
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function(options) {
				this.layout = new AppLayout();
				App.body.show(this.layout);
				
				this.layout.headerRegion.show(new HeaderView());
				
				App.Posts = new PostCollection();
				App.Posts.fetch();
			},
			//gets mapped to in AppRouter's appRoutes
			index : function() {
				var home = new HomeLayout();
				this.layout.mainRegion.show(home);
				
				home.contentRegion.show(new PostListView({
					collection: App.Posts
				}));
				//home.mapRegion
				//home.searchRegion
			},
			postList : function() {
				this.layout.mainRegion.show(new PostListView({
					collection: App.Posts
				}));
			}
		});
	}); 