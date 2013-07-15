/**
 * Posts Module
 */
(function () {

	// Post model
	var Post = Backbone.Model.extend({});

	// Posts list
	var Posts = Backbone.Collection.extend({
			model: Post,		
			url: "api/v2/posts"
	});


	var postsList = new Posts();

	//Post View
	var PostView = Backbone.View.extend({
		
			tagName: "posts",

			events: {
			},
			
			initialize: function() {
					this.template = _.template($("#posts-template").html());
			},
			
			render: function() {
					this.$el.html(this.template(this.model.toJSON()));
					return this;
			},
			
			editpost: function() {
				
			},

			viewpost: function() {

			},

			deletepost: function() {
				var view = this;
				this.model.destroy({
					// Synchronous call; wait for 200 status from server
					wait: true,

					// When the operation succeeds
					success: function(){
						// Delete the view from the listing
						view.$el.fadeOut("fast");
					},

					// When the operation fails
					error: function() {
						// TODO: Show error dialog or other message
					},
				});
			}	
	});

	var PostListView = Backbone.View.extend({
		el: "#posts-list",

		initialize: function() {
			postsList.on("add", this.addPost, this);
			postsList.on("reset", this.addPosts, this);
		},

		addPost: function(post) {
			var view = new PostView({model: post});
			this.$el.append(view.render().el);
		},

		addPosts: function() {
			postsList.each(this.addPost, this);
		}
	});

	// Bootstrap the posts collection
	postsList.fetch();


	//SearchFilter Model
	var SearchFilter = Backbone.Model.extend({
			defaults: {
					"keywords": null,
					"locations": null,
					"category": null,	
			},	
	});

	//SearchFilter collection
	var SearchFilters = Backbone.Collection.extend({
			model: SearchFilter
	});

});
