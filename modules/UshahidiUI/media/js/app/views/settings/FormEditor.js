/**
 * Forms
 *
 * @module     Forms
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'alertify', 'forms/UshahidiForms', 'hbs!templates/settings/FormEditor'],
	function(App, Marionette, _, alertify, BackboneForm, template)
	{
		return Marionette.Layout.extend(
		{
			template: template,
			form: null,

			regions : {
				availableAttributes : '.available-attributes',
				formAttributes : '.form-attributes'
			},

			events : {
				'click .js-edit-attr' : 'toggleEditor',
				'click .js-add-attr' : 'toggleEditor',
				'submit form' : 'saveField'
			},

			initialize : function()
			{
				App.vent.on('form:attribute:edit', this.showEditor, this);
			},

			toggleEditor : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget)
					.closest('.tabs')
						.find('.tab-title')
					.add(this.$('.edit-attribute'))
					.add(this.$('.available-attributes'))
					.toggleClass('active');
			},

			showEditor : function(model)
			{
				ddt.log('FormEditor', 'showEditor', model);

				var $editor = this.$('.js-edit-form'),
					$panel = $editor.closest('.content');

				this.form = new BackboneForm({
					model: model
				});

				if (!$panel.hasClass('active')) {
					this.$('.js-edit-attr').click();
				}

				// Render the form and add it to the view
				this.form.render();

				this.form.$el.append('<button class="save-edit-button  js-save-attr" type="submit">Save</button>');

				// hide the field editor form until activated
				this.$('.js-edit-form').empty().append(this.form.$el);
			},

			saveField : function(e)
			{
				e.preventDefault();

				var data = this.form.getValue();

				ddt.log('FormEditor', 'form data', data);

				// Split options apart since server expects an array
				if (data.options)
				{
					data.options = data.options.split(',');
				}

				this.form.model.set(_.pick(data, 'label', 'options', 'default', 'format', 'required'));
				this.form.model.save({
						wait: true
					})
					.done(function ()
					{
						alertify.success('Field saved');
					})
					.fail(function ()
					{
						alertify.error('Unable to save field, please try again');
					});
			},
		});
	});
