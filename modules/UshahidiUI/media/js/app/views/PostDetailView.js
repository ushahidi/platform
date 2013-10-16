define(['App', 'marionette', 'underscore', 'handlebars', 'alertify', 'text!templates/PostDetail.html'],
	function( App, Marionette, _, Handlebars, alertify, template)
	{
		//CollectionView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),

			events: {
				'click .post-delete': 'deletepost',
				'click .js-post-edit' : 'showEditPost',
				'click .js-post-set' : 'showAddToSet'

			},

			deletepost: function(e)
			{
				var that = this;
				e.preventDefault();
				alertify.confirm('Are you sure you want to delete', function(e)
				{
					if (e)
					{
						that.model.destroy({
						// Wait till server responds before destroying model
						wait: true
						}).done(function()
						{	
							alertify.success('Post has been deleted.');
							Backbone.history.navigate("views/list",{trigger: true});
		
						}).fail(function ()
						{
							alertify.error('Unable to delete Post, please try again');
						});
					}
					else
					{
						alertify.log('Cancelled');
					}
				});
			},
			
			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					isPublished : this.model.isPublished(),
					tags : this.model.getTags(),
					user : this.model.user ? this.model.user.toJSON() : null,
					location : this.model.getLocation()
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
