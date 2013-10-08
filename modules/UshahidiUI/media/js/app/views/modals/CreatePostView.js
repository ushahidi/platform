define([ 'marionette', 'handlebars', 'text!templates/modals/CreatePost.html', 'backbone-forms/backbone-forms', 'util/FormTemplates'],
	function( Marionette, Handlebars, template, BackboneForm)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			events: {
				'submit form' : 'formSubmitted'
			},
			onDomRefresh : function()
			{
				var form = this.form = new BackboneForm({
					model: this.model,
					idPrefix : 'post-',
					className : 'create-post-form',
					fieldsets : [
						{
							name : 'main',
							legend : '',
							fields : ['title', 'content']
						},
						{
							name : 'image'
						},
						{
							name : 'location'
						},
						{
							name : 'permissions',
							legend : '',
							fields : ['status']
						}
					]
				}).render();
				// Set form id, backbone-forms doesn't do it.
				form.$el.attr('id', 'create-post-form');

				this.$('.create-post-options').append(form.el);
			},
			formSubmitted : function (e)
			{
				e.preventDefault();

				/*var data = Syphon.serialize(this);
				this.model.set(data);*/
				this.form.commit();

				// Temporarily hard code form id
				this.model.set('form_id', 1);

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
