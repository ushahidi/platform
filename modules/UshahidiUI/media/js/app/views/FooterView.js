define(['marionette', 'handlebars', 'App', 'text!templates/Footer.html'],
	function(Marionette, Handlebars, App, template)
	{
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			initialize: function() { },
		});
	});