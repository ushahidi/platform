define(['App', 'marionette', 'handlebars', 'text!templates/AppLayout.html'],
	function(App, Marionette, Handlebars, template) {
		return Marionette.Layout.extend(
		{
			template : Handlebars.compile(template),
			regions : {
				headerRegion : "header",
				mainRegion : "#main"
			}
		});
	}); 