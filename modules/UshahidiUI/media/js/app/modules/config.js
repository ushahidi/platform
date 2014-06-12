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
			// Update the config values from the model
			configs[model.id] = _.clone(model.attributes);

			var App = require('App');
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
				apiuri: 'api/v2',
				imagedir: '/media/kohana/images',
				jsdir: '/media/kohana/js',
				cssdir: '/media/kohana/css'
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

				config_models[group]
					.set(values).save()
						.done(onSaveSuccess)
						.fail(onSaveFailure);

				return config_models[group];
			}
		};
	});
