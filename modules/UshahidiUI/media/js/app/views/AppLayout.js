define(['App', 'marionette', 'handlebars', 'text!templates/AppLayout.html', 'text!templates/partials/modal.html'],
	function(App, Marionette, Handlebars, template, modalTemplate) {
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('modal', modalTemplate);
		return Marionette.Layout.extend(
		{
			className: 'App',
			template : Handlebars.compile(template),
			regions : {
				headerRegion : "#headerRegion",
				mainRegion :   "#mainRegion",
				footerRegion : "#footerRegion",
				adminPanel : "#admin-panel"
			}
		});
	}); 