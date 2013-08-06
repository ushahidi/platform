define(['App', 'marionette', 'handlebars', 'text!templates/post.html','App.oauth'],
	function(App, Marionette, Handlebars, template, OAuth)
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
				"click .delete": "deletepost"
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
						view.$el.remove();
						showSuccessMessage('<?php echo __("The Post has been deleted!"); ?>', {flash: true});
					},

					// When the operation fails
					error: function() {
						showFailureMessage("Unable to delete post. Try again later.");
					},
				});
			}
		});
	});

