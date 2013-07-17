define( [ 'App', 'marionette', 'handlebars', 'views/PostItemView', 'text!templates/postlist.html'],
	function( App, Marionette, Handlebars, PostItemView, template) {
		//CollectionView provides some default rendering logic
		return Marionette.CompositeView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),

			initialize: function(params) {
				//console.log(params);
			},
			
			itemView: PostItemView,
			itemViewOptions: {
				//foo: "bar"
			},
			itemViewContainer: '.posts',
			
			events: {
			
			},
			
		});
	});
