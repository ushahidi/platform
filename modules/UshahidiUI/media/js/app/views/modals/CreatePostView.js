define([ 'marionette', 'handlebars', 'text!templates/modals/CreatePost.html', 'backbone-forms/backbone-forms', 'util/FormTemplates'],
	function( Marionette, Handlebars, template, BackboneForm)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize : function ()
			{
				// Set up the form
				this.form = new BackboneForm({
					model: this.model,
					idPrefix : 'post-',
					className : 'create-post-form',
					fieldsets : [
						{
							name : 'main',
							legend : '',
							fields : ['title', 'content'],
							active: true
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
				});
			},
			events: {
				'submit form' : 'formSubmitted',
				'click .js-switch-fieldset' : 'switchFieldSet'
			},
			onDomRefresh : function()
			{
				// Render the form and add it to the view
				this.form.render();

				// Set form id, backbone-forms doesn't do it.
				this.form.$el.attr('id', 'create-post-form');

				this.$('.create-post-options').append(this.form.el);
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
			},
			switchFieldSet : function (e)
			{
				var $el = this.$(e.currentTarget);
				this.$('fieldset').removeClass('active');
				this.$($el.attr('href')).addClass('active');

				e.preventDefault();
			}
		});
	});
