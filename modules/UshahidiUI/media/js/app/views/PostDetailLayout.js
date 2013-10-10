define(['App', 'marionette', 'handlebars', 'text!templates/PostDetailLayout.html'],
	function(App, Marionette, Handlebars, template) {
		return Marionette.Layout.extend(
		{
			className: 'layout-posts',
			template : Handlebars.compile(template),
			regions : {
				mapRegion : '#mapRegion',
				postDetailRegion : '#post-details',
				relatedPostsRegion : '#related-posts'
			}
		});
	});