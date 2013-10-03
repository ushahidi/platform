define([ 'marionette', 'handlebars', 'text!templates/modals/CreatePost.html'],
	function( Marionette, Handlebars, template)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() { }
		});
	});
