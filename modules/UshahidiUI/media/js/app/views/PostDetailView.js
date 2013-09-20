define(['App', 'marionette', 'underscore', 'handlebars', 'text!templates/PostDetail.html'],
	function( App, Marionette, _, Handlebars, template)
	{
		//CollectionView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			
			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					isPublished : this.model.isPublished(),
					tags : this.model.getTags(),
					user : this.model.user.toJSON()
				});
				return data;
			},

			modelEvents : {
				// hack to re-render this view when the model actually loads
				// @todo fix race condition so we don't need this
				'change' : function () { this.render(); }
			}

		});
	});