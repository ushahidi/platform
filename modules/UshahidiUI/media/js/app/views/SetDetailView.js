define([ 'App', 'marionette', 'handlebars', 'text!templates/SetDetail.html', 'text!templates/partials/set-module.html', 'text!templates/partials/pagination.html',],
    function( App, Marionette, Handlebars, template, setModuleTemplate, paginationTemplate)
	{
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('set-module', setModuleTemplate);
		Handlebars.registerPartial('pagination', paginationTemplate);

		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() {
			},
			events : {
			}
		});
	});
