define(['App', 'backbone', 'marionette',
	'views/AppLayout', 'views/HomeLayout', 'views/HeaderView', 'views/FooterView', 'views/WorkspacePanelView', 'views/SearchBarView', 'views/MapView',
	'views/PostListView', 'views/PostDetailView','collections/PostCollection'],
	function(App, Backbone, Marionette,
		AppLayout, HomeLayout, HeaderView, FooterView, WorkspacePanelView, SearchBarView, MapView,
		PostListView, PostDetailView, PostCollection)
	{
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function(options) {
				this.layout = new AppLayout();
				App.body.show(this.layout);
				
				var header = new HeaderView();
				header.on('workspace:toggle', function () {
					App.body.$el.toggleClass('active-workspace')
				});
				
				this.layout.headerRegion.show(header);
				this.layout.footerRegion.show(new FooterView());
				this.layout.workspacePanel.show(new WorkspacePanelView());
				
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
			viewsList : function() {
				App.vent.trigger("page:change", "views/list");
				var home = new HomeLayout();
				this.layout.mainRegion.show(home);
				
				home.contentRegion.show(new PostListView({
					collection: App.Posts
				}));
				// Nothing bound to map region
				home.searchRegion.show(new SearchBarView());
			},
			viewsMap : function() {
				App.vent.trigger("page:change", "views/map");
				var home = new HomeLayout();
				this.layout.mainRegion.show(home);
				
				// Nothing bound to content region
				home.mapRegion.show(new MapView());
				home.searchRegion.show(new SearchBarView());
			},
			postDetail : function(id) {
				App.vent.trigger("page:change", "posts/:id");
				this.layout.mainRegion.show(new PostDetailView({
					model: App.Posts.get(id)	
				}));

			}
		});
	}); 
