define([ 'marionette', 'handlebars', 'backbone.syphon', 'text!templates/modals/CreatePost.html'],
	function( Marionette, Handlebars, Syphon, template)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() { },
			events: {
				'submit form' : 'formSubmitted'
			},
			formSubmitted : function (e)
			{
				var data;

				e.preventDefault();

				data = Syphon.serialize(this);
				this.model.set(data);

				this.model.save()
				.done(function (model, response, options)
					{
						// alertify message about 'saved'
						//
					})
				.fail(function (response, xhr, options)
					{
						// validation error
					});

				this.trigger('close');
			}
		});
	});
