define(['App', 'marionette', 'underscore', 'handlebars', 'alertify', 'text!templates/PostListItem.html'],
	function(App, Marionette, _, Handlebars, alertify, template)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post',
			
			events: {
				'click .post-delete': 'deletepost',
				'click .js-post-edit' : 'showEditPost',
				'click .js-post-set' : 'showAddToSet'
			},

			// @todo add confirmation dialog
			deletepost: function(e)
			{
				alertify.confirm("Are you sure you want to delete", function(e)
				{
					e.preventDefault();
					this.model.destroy({
						// Wait till server responds before destroying model
						wait: true
					});

					if(true){
						alertify.success("Post has been deleted.");

					} else {
						alertify.error("Please try again");
					}
				});
			},
			
			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					isPublished : this.model.isPublished(),
					tags : this.model.getTags(),
					user : this.model.user ? this.model.user.toJSON() : null
				});
				return data;
			},
			showEditPost : function ()
			{
				App.vent.trigger('post:edit', this.model);
			},
			showAddToSet : function ()
			{
				App.vent.trigger('post:set', this.model);
			}
		});
	});
