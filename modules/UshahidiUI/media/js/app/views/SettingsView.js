/**
 * Settings
 *
 * @module     SettingsView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'App', 'marionette', 'handlebars', 'jquery', 'alertify', 'underscore', 'text!templates/Settings.html', 'models/ConfigModel'],
	function( App, Marionette, Handlebars, $, alertify, _, template, ConfigModel)
	{
		var updateConfig = function(group, hash)
		{
			var model = new ConfigModel({'@group': group});


			model.set(hash).save()
				.done(function (/* model, response, options*/)
					{
						var oldGroup = _.clone(App.config[group]),
							newConfig = {};
						
						newConfig[group] = _.extend(oldGroup, hash);

						// trigger a config update throughout the app
						App.updateConfig(newConfig);

						alertify.success('Settings saved.');

						// After saving stay on the same page till there is a
						// dashboard to navigate to.
					})
				.fail(function (response /*, xhr, options*/)
					{
						alertify.error('Unable to save settings, please try again.');
						if (response.errors) {
							ddt.log('debug', response.errors);
						}
					});
		};

		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() {

			},
			events : {
				'submit .settings-site form' : 'formSubmitSite',
				'submit .settings-features form' : 'formSubmitFeatures'
			},
			serializeData : function()
			{
				return {
					site : App.config.site,
					features : App.config.features
				};
			},
			formSubmitSite : function(e)
			{
				e.preventDefault();

				var form = this.$(e.target),
					data = form.serializeArray(),
					group = 'site',
					hash = {};

				_.each(data, function(input) {
					hash[input.name] = input.value;
				});

				ddt.log('settings', 'update', group, hash);
				updateConfig(group, hash);
			},
			formSubmitFeatures : function(e)
			{
				e.preventDefault();

				var form = this.$(e.target),
					data = form.serializeArray(),
					group = 'features',
					hash = {};

				_.each(data, function(input) {
					// all feature values are boolean!
					hash[input.name] = (input.value === 'true');
				});

				ddt.log('settings', 'update', group, hash);
				updateConfig(group, hash);
			}
		});
	});
