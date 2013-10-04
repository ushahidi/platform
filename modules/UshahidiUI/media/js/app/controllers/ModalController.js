define(['App', 'backbone', 'marionette',
	'views/modals/CreatePostView', 'views/modals/EditPostView', 'views/modals/AddToSetView', 'views/modals/CreateSetView'],
	function(App, Backbone, Marionette,
		PostCreateView, PostEditView, AddToSetView, CreateSetView)
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
				App.vent.on('post:createSet', this.setCreate, this);
			},
			postCreate : function ()
			{
				this.modal.show(new PostCreateView());
			},
			postEdit : function (post)
			{
				this.modal.show(new PostEditView({
					model : post
				}));
			},
			addToSet : function (post)
			{
				this.modal.show(new AddToSetView({
					model : post
				}));
			},
			setCreate : function (post)
			{
				this.modal.show(new CreateSetView({
					model : post
				}));
			}
		});
	});
