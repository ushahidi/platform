/**
 * Ushahidi Application
 *
 * @module     App
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'marionette', 'underscore', 'util/App.oauth', 'util/App.handlebars', 'foundation-loader'],
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
			// config loaded at runtime to avoid any possible circular dependencies
			var config = require('modules/config'),
				features = config.get('features');
			return Boolean(features[name]);
		};

		App.Behaviors  = {
			//PageableView : PageableView
		};

		Marionette.Behaviors.behaviorsLookup = function() {
			return App.Behaviors;
		};

		//Organize Application into regions corresponding to DOM elements
		//Regions can contain views, Layouts, or subregions nested as necessary
		App.addRegions(
		{
			body : 'body'
		});

		App.on('initialize:after', function(/*options*/)
		{
			var config = require('modules/config');

			if (App.feature('pushstate'))
			{
				require(['util/App.pushState'], function(pushStateInit) { pushStateInit(); });
			}

			if (Backbone.history)
			{
				Backbone.history.start({
					pushState : App.feature('pushstate'),
					root : config.get('basepath')
				});
			}

			// Init foundation
			$(document).foundation();
		});

		return App;
	});
