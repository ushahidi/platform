define([ 'marionette', 'handlebars', 'text!templates/modals/AddToSet.html', 'text!templates/partials/set-module-mini.html'],
	function( Marionette, Handlebars, template, setModuleMiniTemplate)
	{
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('set-module-mini', setModuleMiniTemplate);
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() { }
		});
	});
