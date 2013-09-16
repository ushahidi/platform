define(['App', 'marionette', 'handlebars', 'text!templates/PostDetail.html'],
	function( App, Marionette, Handlebars, template)
	{
		//CollectionView provides some default rendering logic
		return Marionette.ItemView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			
			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					published : this.model.published(),
					tags : this.model.tags()
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
