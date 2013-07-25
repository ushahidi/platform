define(['App', 'backbone', 'marionette',
	'views/AppLayout', 'views/HomeLayout', 'views/HeaderView', 'views/FooterView', 'views/AdminPanelView', 'views/SearchBarView', 'views/MapView',
	'views/PostListView', 'views/PostDetailView','collections/PostCollection'],
	function(App, Backbone, Marionette,
		AppLayout, HomeLayout, HeaderView, FooterView, AdminPanelView, SearchBarView, MapView,
		PostListView, PostDetailView, PostCollection)
	{
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function(options) {
				this.layout = new AppLayout();
				App.body.show(this.layout);
				
				this.layout.headerRegion.show(new HeaderView());
				this.layout.footerRegion.show(new FooterView());
				this.layout.adminPanel.show(new AdminPanelView());
				
				App.Posts = new PostCollection();
				App.Posts.fetch();
			},
			//gets mapped to in AppRouter's appRoutes
			index : function() {
				App.vent.trigger("page:change", "index");
				var home = new HomeLayout();
				this.layout.mainRegion.show(home);
				
				home.contentRegion.show(new PostListView({
					collection: App.Posts
				}));
				home.mapRegion.show(new MapView());
				home.searchRegion.show(new SearchBarView());
			},
			postList : function() {
				App.vent.trigger("page:change", "posts");
				this.layout.mainRegion.show(new PostListView({
					collection: App.Posts
				}));
			},
			postDetail : function() {
				App.vent.trigger("page:change", "posts/:id");
				this.layout.mainRegion.show(new PostDetailView({
					collection: App.Posts
				}));
			}
		});
	}); 
