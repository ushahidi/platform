/**
 * Data Sources
 *
 * @module     DataSources
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'underscore', 'alertify', 'text!templates/settings/DataProviderConfig.html', 'forms/UshahidiForms'],
	function(App, Marionette, Handlebars, _, alertify, template, BackboneForm)
	{
		return Marionette.CompositeView.extend(
		{
			template: Handlebars.compile(template),
			events: {
				'submit form' : 'formSubmitted'
			},
			initialize: function (options)
			{
				this.configModel = options.configModel;
				this.dataProviderModel = options.dataProviderModel;

				// Set up the form
				this.form = new BackboneForm({
					schema: this.dataProviderModel.schema(),
					data: this.configModel.get(this.dataProviderModel.id),
					idPrefix : 'config-',
					className : 'data-provider-config-form',
					});
			},
			onDomRefresh : function()
			{
				// Render the form and add it to the view
				this.form.render();

				// Set form id, backbone-forms doesn't do it.
				this.form.$el.attr('id', 'data-provider-config-form');

				this.$('.js-form').append(this.form.el);
			},
			formSubmitted : function (e)
			{
				var request;

				e.preventDefault();

				this.configModel.set(this.dataProviderModel.id, this.form.getValue());

				request = this.configModel.save();
				if (request)
				{
					request
						.done(function (/*model, response, options*/)
							{
								alertify.success('Configuration saved.');
								App.appRouter.navigate('messages/settings', { trigger : true });
							})
						.fail(function (response /*, xhr, options*/)
							{
								alertify.error('Unable to save configuration, please try again.');
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
					alertify.error('Unable to save configuration, please try again.');
					console.log(this.configModel.validationError);
				}
			},
		});
	});
