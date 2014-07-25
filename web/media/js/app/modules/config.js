/**
 * Configuration Module
 *
 * @module     config
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'alertify', 'models/ConfigModel'],
	function(_, alertify, ConfigModel)
	{
		function onSaveSuccess(model/*response, options*/)
		{
			var App = require('App'),
				group = model.get(model.idAttribute);

			// Update the config values from the model
			configs[group] = _.clone(model.attributes);

			App.vent.trigger('config:change', configs);
			alertify.success('Settings saved.');
		}

		function onSaveFailure(response /*, xhr, options*/)
		{
			alertify.error('Unable to save settings, please try again.');
			if (response.errors) {
				ddt.log('Config', response.errors);
			}
		}

		var readonly_config = {
				baseurl: '/',
				apiurl: '/api/v2/',
				imagedir: '/media/images',
				jsdir: '/media/js',
				cssdir: '/media/css'
			},
			configs = _.extend({}, readonly_config, window.config),
			config_models = {};

		_.each(configs, function(values, group) {
			if (!readonly_config.hasOwnProperty(group) && typeof configs[group] === 'object') {
				// Load any existing objects in the config as config models
				config_models[group] = new ConfigModel({'@group': group}).set(values);
			}
		});

		return {
			get: function(group)
			{
				if (configs.hasOwnProperty(group)) {
					return configs[group];
				}
				return {};
			},
			set: function(group, values)
			{
				if (readonly_config.hasOwnProperty(group)) {
					throw 'Attempted to change read-only configuration: ' + group;
				}
				if (!config_models.hasOwnProperty(group)) {
					return null;
				}

				config_models[group].save(values, {
					success: onSaveSuccess,
					error: onSaveFailure
				});

				return config_models[group];
			}
		};
	});
