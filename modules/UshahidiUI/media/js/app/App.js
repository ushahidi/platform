/**
 * Ushahidi Application
 *
 * @module     App
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'marionette', 'underscore', 'util/App.oauth', 'util/App.handlebars', 'backbone-model-factory', 'foundation-loader'],
	function($, Backbone, Marionette, _, OAuth)
	{
		var App = new Backbone.Marionette.Application();

		// Save oauth object in App - just in case
		App.oauth = OAuth;

		// Quick helper to determine whether or not oauth is logged in
		App.loggedin = function() {
			return OAuth.getClientType() === 'user';
		};

		// Quick helper to determine if a feature is enabled
		App.feature = function(name) {
			if (!App.config || !App.config.features) {
				return false;
			}
			return Boolean(App.config.features[name]);
		};

		//Organize Application into regions corresponding to DOM elements
		//Regions can contain views, Layouts, or subregions nested as necessary
		App.addRegions(
		{
			body : 'body'
		});

		App.on("initialize:after", function(/*options*/)
		{
			if (Backbone.history)
			{
				Backbone.history.start();
			}

			// Init foundation
			$(document).foundation();
		});

		// Global config params
		App.config = _.extend({
			baseurl : '/',
			apiuri : 'api/v2',
			imagedir : '/media/kohana/images',
			jsdir : '/media/kohana/js',
			cssdir : '/media/kohana/css'
		}, window.config);

		/**
		 * Update App.config
		 * @param {Object} newConfig new config vars to merge into old config
		 */
		App.updateConfig = function (newConfig)
		{
			_.extend(this.config, newConfig);
			this.vent.trigger('config:change', this.config);
			return this.config;
		};

		return App;
	});
