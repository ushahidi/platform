/**
 * Ushahidi Application
 *
 * @module     App
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'marionette', 'underscore', 'util/App.oauth', 'util/App.handlebars', 'foundation-loader'],
	function($, Backbone, Marionette, _, Handlebars, OAuth)
	{
		var App = new Backbone.Marionette.Application();

		// Save oauth object in App - just in case
		App.oauth = OAuth;

		//Organize Application into regions corresponding to DOM elements
		//Regions can contain views, Layouts, or subregions nested as necessary
		App.addRegions(
		{
			body : 'body'
		});

		function isMobile()
		{
			var ua = (navigator.userAgent || navigator.vendor || window.opera, window, window.document);
			return (/iPhone|iPod|iPad|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
		}

		App.mobile = isMobile();

		App.addInitializer(function(/*options*/)
		{
			Backbone.history.start();

			// Init foundation
			$(document).foundation();
		});

		// Global config params
		App.config = _.extend({
			baseurl : '/',
			apiurl : '/api/v2/',
			imagedir : '/media/kohana/images',
			jsdir : '/media/kohana/js',
			cssdir : '/media/kohana/css'
		}, window.config);

		return App;
	});