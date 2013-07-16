define( [ 'App', 'marionette', 'handlebars', 'text!templates/post.html'],
	function( App, Marionette, Handlebars, template) {
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',

			initialize: function(params) {
				//console.log(params);
			},
			
			events: {
			
			},
			
			editpost: function() {
				
			},

			viewpost: function() {

			},

			deletepost: function() {
				var view = this;
				this.model.destroy({
					// Wait till server responds before destroying model
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
	});

