define(['App', 'marionette', 'handlebars', 'text!templates/PostListItem.html'],
	function(App, Marionette, Handlebars, template)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post',

			initialize: function(params) {
				//console.log(params);
			},
			
			events: {
				"click .post-delete": "deletepost"
			},

			// @todo add confirmation dialog
			deletepost: function(e) {
				e.preventDefault();
				this.model.destroy({
					// Wait till server responds before destroying model
					wait: true
				});
			},
			
			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					published : this.model.published(),
					tags : this.model.tags()
				});
				return data;
			}
		});
	});

