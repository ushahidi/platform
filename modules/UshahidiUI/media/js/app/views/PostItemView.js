define(['App', 'marionette', 'underscore', 'handlebars', 'text!templates/PostListItem.html'],
	function(App, Marionette, _, Handlebars, template)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post',
			
			events: {
				'click .post-delete': 'deletepost'
			},

			// @todo add confirmation dialog
			deletepost: function(e)
			{
				e.preventDefault();
				this.model.destroy({
					// Wait till server responds before destroying model
					wait: true
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
			}
		});
	});
