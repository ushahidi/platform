define([ 'App', 'marionette', 'handlebars', 'text!templates/SetDetail.html', 'text!templates/partials/set-module.html'],
    function( App, Marionette, Handlebars, template, setModuleTemplate)
	{
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('set-module', setModuleTemplate);
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() {
			},
			events : {
			}
		});
	});
