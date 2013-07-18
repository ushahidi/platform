define( [ 'App', 'marionette', 'handlebars', 'text!templates/post.html'],
	function( App, Marionette, Handlebars, template) {
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
				"click li.edit > a": "editpost",
				"click li.view > a": "viewpost",
				"click li.delete > a": "deletepost"
			
			},
			
			editpost: function() {
				// executed when edit link is clicked

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

