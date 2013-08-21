define(['App', 'marionette', 'handlebars', 'views/PostItemView','text!templates/RelatedPosts.html'],
	function( App, Marionette, Handlebars, PostItemView, template)
	{
		return Marionette.CompositeView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			initialize: function() {
			},
			
			itemView: PostItemView,
			
		});
	});
