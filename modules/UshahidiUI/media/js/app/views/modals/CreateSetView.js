define([ 'marionette', 'handlebars', 'text!templates/modals/CreateSet.html'],
	function( Marionette, Handlebars, template)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() { },
			events : {
				'click .js-visiblity-private' : 'toggleVisibility',
				'click .js-visiblity-public' : 'toggleVisibility'
			},
			toggleVisibility : function (e)
			{
				console.log('foobar');
				e.preventDefault();
				var $el = this.$(e.currentTarget);
				$el.toggleClass('none');
			}
		});
	});
