define(['App', 'marionette', 'handlebars', 'text!templates/PostDetail.html'],
	function( App, Marionette, Handlebars, template)
	{
		//CollectionView provides some default rendering logic
		return Marionette.ItemView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			
			itemViewContainer: '.post-details'
		});
	});