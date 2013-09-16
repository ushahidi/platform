define(['App', 'backbone', 'marionette',

	'views/AppLayout', 'views/HomeLayout', 'views/PostDetailLayout',
	'views/HeaderView', 'views/FooterView', 'views/WorkspacePanelView', 'views/SearchBarView', 
	'views/MapView','views/PostListView','views/PostDetailView','views/RelatedPostsView',
	'collections/PostCollection','collections/TagCollection','collections/FormCollection','models/PostModel'],
	function(App, Backbone, Marionette,
		AppLayout, HomeLayout, PostDetailLayout,
		HeaderView, FooterView, WorkspacePanelView, SearchBarView, MapView,
		PostListView, PostDetailView, RelatedPostsView,
		PostCollection, TagCollection, FormCollection, PostModel)
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
				
				App.Collections = {};
				App.Collections.Posts = new PostCollection();
				App.Collections.Posts.fetch();
				App.Collections.Tags = new TagCollection();
				App.Collections.Tags.fetch();
				App.Collections.Forms = new FormCollection();
				App.Collections.Forms.fetch();
				
				App.homeLayout = new HomeLayout();
			},
			//gets mapped to in AppRouter's appRoutes
			index : function() {
				App.vent.trigger("page:change", "index");
				this.layout.mainRegion.show(App.homeLayout);
				
				App.homeLayout.contentRegion.show(new PostListView({
					collection: App.Collections.Posts
				}));
				App.homeLayout.mapRegion.show(new MapView());
				App.homeLayout.searchRegion.show(new SearchBarView());
			},
			viewsList : function() {
				App.vent.trigger("page:change", "views/list");
				this.layout.mainRegion.show(App.homeLayout);
				
				App.homeLayout.contentRegion.show(new PostListView({
					collection: App.Collections.Posts
				}));
				// Nothing bound to map region
				App.homeLayout.mapRegion.close();
				App.homeLayout.searchRegion.show(new SearchBarView());
			},
			viewsMap : function() {
				App.vent.trigger("page:change", "views/map");
				this.layout.mainRegion.show(App.homeLayout);
				
				// Nothing bound to content region
				App.homeLayout.contentRegion.close();
				App.homeLayout.mapRegion.show(new MapView());
				App.homeLayout.searchRegion.show(new SearchBarView());
			},
			postDetail : function(id) {
				App.vent.trigger("page:change", "posts/:id");
				var postDetailLayout = new PostDetailLayout();
				this.layout.mainRegion.show(postDetailLayout);
				
				// @todo improve this to avoid double loading of model (and race conditions)
				var model = App.Collections.Posts.get(id);
				if (typeof model === 'undefined')
				{
					model = new PostModel({id: id});
					model.fetch();
				}

				// @TODO find a way to reuse post detail views
				postDetailLayout.mapRegion.show(new MapView());
				postDetailLayout.postDetailRegion.show(new PostDetailView({
					model: model
				}));	
				postDetailLayout.relatedPostsRegion.show(new RelatedPostsView());
			}
		});
	}); 
