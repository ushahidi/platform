define(['App', 'marionette', 'handlebars', 'text!templates/RelatedPosts.html'],
	function( App, Marionette, Handlebars, template)
	{
		//CollectionView provides some default rendering logic
		return Marionette.ItemView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			initialize: function(options) {
			},
			
			itemViewContainer: '.related-posts',
			
		});
	});
