define( [ 'App', 'marionette', 'handlebars','text!templates/postdetail.html',
'App.oauth'],
	function( App, Marionette, Handlebars, template, OAuth, PostModel) {
		//CollectionView provides some default rendering logic
		return Marionette.ItemView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			initialize: function(options) {
			},
			
			itemViewContainer: '.post-details',
			
		});
	});
