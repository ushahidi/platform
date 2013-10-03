define(['App', 'backbone', 'marionette',
	'views/modals/CreatePostView', 'views/modals/EditPostView', 'views/modals/AddToSetView'],
	function(App, Backbone, Marionette,
		PostCreateView, PostEditView, AddToSetView)
	{
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function(options)
			{
				// Store modal region we're controlling
				this.modal = options.modal;
				
				App.vent.on('post:create', this.postCreate, this);
				App.vent.on('post:edit', this.postEdit, this);
				App.vent.on('post:set', this.addToSet, this);
			},
			postCreate : function ()
			{
				this.modal.show(new PostCreateView());
				Backbone.$(this.modal.el).foundation('reveal', 'open');
			},
			postEdit : function (post)
			{
				this.modal.show(new PostEditView({
					model : post
				}));
				Backbone.$(this.modal.el).foundation('reveal', 'open');
			},
			addToSet : function (post)
			{
				this.modal.show(new AddToSetView({
					model : post
				}));
				Backbone.$(this.modal.el).foundation('reveal', 'open');
			}
		});
	});
