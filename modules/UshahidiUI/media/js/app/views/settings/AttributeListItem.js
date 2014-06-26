/**
 * Attribute List Item View
 *
 * @module     AttributeListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'marionette', 'alertify', 'forms/UshahidiForms', 'hbs!templates/settings/AttributeListItem'],
	function(_, Marionette, alertify, BackboneForm, template)
	{
		return Marionette.ItemView.extend(
		{
			template: template,
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
				'click .js-delete-field' : 'deleteField'
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
						schema: this.model.previewSchema(),
						data: {
							preview : this.model.get('default')
						},
						idPrefix : 'attribute-',
						className : 'attribute-form',
					});
				} catch (err) {
					ddt.log('FormEditor', 'could not create form for attr', err);
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

				// hide the field editor form until activated
				this.$('.js-form-input').empty().append(this.form.$el);
			},

			toggleEdit : function(e)
			{
				e.preventDefault();

				this.trigger('edit', this.model);
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
