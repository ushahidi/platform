/**
 * Edit / Create User
 *
 * @module     EditUserView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'App', 'marionette', 'underscore', 'alertify', 'hbs!templates/modals/EditUser',
	'forms/UshahidiForms', 'backbone-validation'],
	function( App, Marionette, _, alertify, template, BackboneForm, BackboneValidation)
	{
		return Marionette.ItemView.extend( {
			template: template,
			initialize : function ()
			{
				// Set up the form
				this.form = new BackboneForm({
					model: this.model,
					idPrefix : 'user-',
					className : 'edit-user-form',
					});
				BackboneValidation.bind(this, {
					valid: function(/* view, attr */)
					{
						// Do nothing, displaying errors is handled by backbone-forms
					},
					invalid: function(/* view, attr, error */)
					{
						// Do nothing, displaying errors is handled by backbone-forms
					}
				});
			},

			events: {
				'submit form' : 'formSubmitted'
			},

			onDomRefresh : function()
			{
				// Render the form and add it to the view
				this.form.render();

				// Set form id, backbone-forms doesn't do it.
				this.form.$el.attr('id', 'edit-user-form');

				this.$('.user-form-wrapper').append(this.form.el);
			},
			formSubmitted : function (e)
			{
				var that = this,
					errors,
					request;

				e.preventDefault();

				errors = this.form.commit({ validate: true });

				if (! errors)
				{
					request = this.model.save();
					if (request)
					{
						request
							.done(function ()
								{
									alertify.success('User details saved.');

									that.trigger('close');
								})
							.fail(function (response /*, xhr, options*/)
								{
									alertify.error('Unable to save user details, please try again.');
									// validation error
									if (response.errors)
									{
										// @todo Display this error somehow
										console.log(response.errors);
									}
								});
					}
					else
					{
						alertify.error('Unable to save user details, please try again.');
						console.log(this.model.validationError);
					}
				}
			},
			onClose : function ()
			{
				BackboneValidation.unbind(this);
				App.Collections.Users.fetch();
			},

			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(),
				{
					isNew : this.model.isNew()
				});
				return data;
			},
		});
	});