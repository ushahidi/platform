define(['App', 'marionette', 'handlebars', 'text!templates/HomeLayout.html'],
	function(App, Marionette, Handlebars, template) {
		return Marionette.Layout.extend(
		{
			template : Handlebars.compile(template),
			regions : {
				mapRegion : "#map",
				searchRegion : "#search-bar",
				contentRegion : "#post-list-view"
			}
		});
	}); 