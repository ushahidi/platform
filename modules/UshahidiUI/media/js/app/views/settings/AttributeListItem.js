/**
 * Attribute List Item View
 *
 * @module     AttributeListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'handlebars', 'marionette', 'alertify', 'forms/UshahidiForms', 'text!templates/settings/AttributeListItem.html'],
	function(_, Handlebars, Marionette, alertify, BackboneForm, template)
	{
		return Marionette.ItemView.extend(
		{
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-attribute',

			form: null,
			attributes : function ()
			{
				var attributes = {
					'data-attribute-type' : this.model.get('type'),
					'data-attribute-input' : this.model.get('input'),
					'data-attribute-label' : this.model.get('label'),
				};

				if (this.model.isNew()) {
					attributes['data-attribute-new'] = true;
				} else {
					attributes['data-attribute-id'] = this.model.get('id');
				}

				return attributes;
			},

			modelEvents: {
				'sync': 'render'
			},

			events: {
				'click .js-edit-field' : 'toggleEdit',
				'click .js-cancel-edit' : 'toggleEdit',
				'click .js-delete-field' : 'deleteField',
				'submit form' : 'saveField'
			},

			initialize : function (/*options*/)
			{
				// BackboneValidation.bind(this, {
				// 	valid: function(/* view, attr */)
				// 	{
				// 		// Do nothing, displaying errors is handled by backbone-forms
				// 	},
				// 	invalid: function(/* view, attr, error */)
				// 	{
				// 		// Do nothing, displaying errors is handled by backbone-forms
				// 	}
				// });
			},

			serializeData: function ()
			{
				var input = this.model.get('input'),
					type = this.model.get('type'),
					data;

				if (input === 'textarea') {
					input = 'TextArea';
				} else if (input === 'datetime') {
					input = 'DateTime';
				} else {
					// JS equivalent of PHP's ucfirst()
					input = input.charAt(0).toUpperCase() + input.substr(1);
				}
				type = type.charAt(0).toUpperCase() + type.substr(1);

				data = _.extend(this.model.toJSON(), {
					input : input,
					type : type
				});
				return data;
			},

			buildForm: function ()
			{
				try {
					this.form = new BackboneForm({
						schema: this.model.schema(),
						data: _.extend(this.model.toJSON(), {
							preview : this.model.get('default')
						}),
						idPrefix : 'attribute-',
						className : 'attribute-form',
					});
				} catch (err) {
					ddt.log('Forms', 'could not create form for attr', err);
				}
			},

			onDomRefresh : function()
			{
				// Create the form if we haven't yet
				if (! this.form)
				{
					this.buildForm();
				}

				// Render the form and add it to the view
				this.form.render();

				var $form = this.form.$el;

				// add a cancel button to the form
                // add a submit button to the form
                // todo: use "submitButton: title" in Backbone.Form v0.15
				$form.append('<div class="form-edit-cancel"><button class="cancel-edit-button  js-cancel-edit">Cancel</button></div><div class="form-edit-save"><button class="save-edit-button" type="submit">Save</button></div>');

				// hide the field editor form until activated
				this.$('.js-form')
					.empty()
					.addClass('hide')
					.append($form);
			},

			toggleEdit : function(e)
			{
				e.preventDefault();

				// Reset the form data
				this.form.setValue(_.extend(this.model.toJSON(), {
					preview : this.model.get('default'),
				}));
				// Show/Hide the form
				this.$('.js-form').toggleClass('hide');
				this.form.trigger('dom:refresh');
			},

			saveField : function(e)
			{
				e.preventDefault();

				var data = this.form.getValue();

				ddt.log('Forms', 'form data', data);
				// Split options apart since server expects an array
				if (data.options)
				{
					data.options = data.options.split(',');
				}

				this.model.set(_.pick(data, 'label', 'options', 'default', 'format', 'required'));
				this.model.save({
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

			deleteField: function(e)
			{
				var that = this;
				e.preventDefault();
				alertify.confirm('Are you sure you want to delete?', function(e)
				{
					if (e)
					{
						that.model
							.destroy({
								// Wait till server responds before destroying model
								wait: true
							})
							.done(function()
							{
								alertify.success('Field has been deleted');
							})
							.fail(function ()
							{
								alertify.error('Unable to delete field, please try again');
							});
					}
					else
					{
						alertify.log('Delete cancelled');
					}
				});
			}
		});
	});
