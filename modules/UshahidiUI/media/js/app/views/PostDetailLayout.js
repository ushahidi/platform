define(['App', 'marionette', 'handlebars', 'text!templates/PostDetailLayout.html'],
	function(App, Marionette, Handlebars, template) {
		return Marionette.Layout.extend(
		{
			className: 'layout-posts',
			template : Handlebars.compile(template),
			regions : {
				mapRegion : "#mapRegion",
				postdetailRegion : "#post-detail",
				relatedpostsRegion : "#related-posts"
			}
		});
	}); 
