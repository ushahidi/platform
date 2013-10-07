define([ 'marionette', 'handlebars', 'text!templates/modals/CreateSet.html'],
	function( Marionette, Handlebars, template)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() { }
		});
	});
