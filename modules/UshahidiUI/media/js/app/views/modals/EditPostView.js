define([ 'marionette', 'handlebars', 'text!templates/modals/EditPost.html'],
	function( Marionette, Handlebars, template)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() { },
			className: 'edit-post'
		});
	});
