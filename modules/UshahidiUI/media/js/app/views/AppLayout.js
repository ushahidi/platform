define(['App', 'marionette', 'handlebars', 'text!templates/AppLayout.html'],
	function(App, Marionette, Handlebars, template) {
		return Marionette.Layout.extend(
		{
			className: 'App',
			template : Handlebars.compile(template),
			regions : {
				headerRegion : "#headerRegion",
				mainRegion :   "#main",
				footerRegion : "#footerRegion",
				adminPanel : "#admin-panel"
			}
		});
	}); 