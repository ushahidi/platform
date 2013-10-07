	function( Marionette, Handlebars, Syphon, BackboneForm, template)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			events: {
				'submit form' : 'formSubmitted'
			},
			onDomRefresh : function()
			{
				var form = new BackboneForm({
					model: this.model,
					idPrefix : 'post-',
					template : Handlebars.compile('<form data-fieldsets id="create-post-form"></form>'),
				}).render();

				this.$('.create-post-options').append(form.el);
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
