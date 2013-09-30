define(['App', 'marionette', 'handlebars', 'text!templates/HomeLayout.html'],
	function(App, Marionette, Handlebars, template)
	{
		return Marionette.Layout.extend(
		{
			className: 'layout-home',
			template : Handlebars.compile(template),
			regions : {
				mapRegion : '#mapRegion',
				searchRegion : '#search-bar',
				contentRegion : '#post-list-view'
			}
		});
	});