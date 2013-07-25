define( [ 'App', 'marionette', 'handlebars','text!templates/postdetail.html', 'App.oauth', 'models/PostModel'],
	function( App, Marionette, Handlebars, template, OAuth, PostModel) {
		//CollectionView provides some default rendering logic
		return Marionette.ItemView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			initialize: function(options) {
			},
			id: 'post',
			className: 'post-details',
			
			itemViewContainer: '.post-details',

			/*
			onRender: function()
			{
				var model = new PostModel();
				model.fetch(
				{
					success: function () {
						console.log(that.model.toJSON());
					},
					error: function() {
						console.log('Failed to fetch!');
					}
				});
							
				return this;
			}
			*/
			
		});
	});
