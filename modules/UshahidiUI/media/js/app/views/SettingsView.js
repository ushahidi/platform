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
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() {

			},
			events : {
				'submit form' : 'formSubmitted',
			},
			serializeData : function()
			{
				return {
					site_name : App.config.site.site_name,
					owner_name : App.config.site.owner_name,
				};
			},
			formSubmitted : function(e)
			{
				e.preventDefault();

				var site_name = this.$('#site_name').val(),
					owner_name = this.$('#owner_name').val(),
					site_name_model,
					owner_name_model;

				site_name_model = new ConfigModel({
					config_key: 'site_name',
					group_name: 'site',
					config_value : site_name
				});
				site_name_model.id = 'site_name';

				owner_name_model = new ConfigModel({
					config_key: 'owner_name',
					group_name: 'site',
					config_value : owner_name
				});
				owner_name_model.id = 'owner_name';

				$.when(site_name_model.save(), owner_name_model.save())
					.done(function (/* model, response, options*/)
						{
							var newSite;

							alertify.success('Settings saved.');

							// Update config
							newSite = _.clone(App.config.site);
							newSite.site_name = site_name,
							newSite.owner_name = owner_name;
							App.updateConfig({ site : newSite });

							window.history.back();
						})
					.fail(function (response /*, xhr, options*/)
						{
							alertify.error('Unable to save settings, please try again.');
							// validation error
							if (response.errors)
							{
								// @todo Display this error somehow
								console.log(response.errors);
							}
						});
			}
		});
	});
