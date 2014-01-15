/**
 * Ushahidi RequireJS initialisation and config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

require.config(
{
	// Set baseurl based on config
	baseUrl : (window.config && window.config.jsdir) ? window.config.jsdir + '/app' : './media/kohana/js/app',
	// 3rd party script alias names (Easier to type 'jquery' than 'libs/jquery, etc')
	// probably a good idea to keep version numbers in the file names for updates checking
	paths :
	{
		'jquery' : '../libs/jquery',
		'underscore' : '../libs/lodash',
		'backbone' : '../libs/backbone',
		'marionette' : '../libs/backbone.marionette',
		'handlebars' : '../libs/handlebars',
		'leaflet' : '../libs/leaflet',
		'l.geosearch' : '../libs/L.GeoSearch/src/js',
		'leaflet-locatecontrol' : '../libs/leaflet-locatecontrol/src',
		'jso2' : '../libs/jso2',
		'store' : '../libs/jso2/store',
		'utils' : '../libs/jso2/utils',
		'moment' : '../libs/moment',
		'underscore.string' : '../libs/underscore.string',
		'foundation' : '../libs/foundation',
		'foundation-loader' : '../libs/foundation-loader',
		'backbone.validateAll' : '../libs/Backbone.validateAll',
		'backbone-pageable' : '../libs/backbone-pageable',
		'handlebars-paginate' : '../libs/handlebars-paginate',
		'backbone.syphon' : '../libs/backbone.syphon',
		'backbone-forms' : '../libs/backbone-forms/backbone-forms',
		'bf' : '../libs/backbone-forms/',
		'backbone-validation' : '../libs/backbone-validation-amd',
		'alertify' : '../libs/alertify',
		'text' : '../libs/requirejs-text'
	},
	// Sets the configuration for your third party scripts that are not AMD compatible
	shim :
	{
		'backbone' :
		{
			'deps' : ['underscore', 'jquery'],
			// Exports the global window.Backbone object
			'exports' : 'Backbone'
		},
		'marionette' :
		{
			'deps' : ['underscore', 'backbone', 'jquery'],
			// Exports the global window.Marionette object
			'exports' : 'Marionette'
		},
		'handlebars' :
		{
			'exports' : 'Handlebars'
		},
		// Backbone.validateAll plugin (https://github.com/gfranko/Backbone.validateAll)
		'backbone.validateAll' : ['backbone'],

		'leaflet': {
			deps: ['jquery'],
			exports: 'L'
		},
		'l.geosearch/l.control.geosearch': {
			deps: ['leaflet'],
			exports: 'L'
		},
		'l.geosearch/l.geosearch.provider.bing': {
			deps: ['leaflet', 'l.geosearch/l.control.geosearch'],
			exports: 'L'
		},
		'l.geosearch/l.geosearch.provider.esri': {
			deps: ['leaflet', 'l.geosearch/l.control.geosearch'],
			exports: 'L'
		},
		'l.geosearch/l.geosearch.provider.google': {
			deps: ['leaflet', 'l.geosearch/l.control.geosearch'],
			exports: 'L'
		},
		'l.geosearch/l.geosearch.provider.nokia': {
			deps: ['leaflet', 'l.geosearch/l.control.geosearch'],
			exports: 'L'
		},
		'l.geosearch/l.geosearch.provider.openstreetmap': {
			deps: ['leaflet', 'l.geosearch/l.control.geosearch'],
			exports: 'L'
		},
		'leaflet-locatecontrol/L.Control.Locate.js': {
			deps: ['leaflet'],
			exports: 'L'
		},

		'moment': {
			exports: 'moment'
		},

		'foundation/foundation' : {deps: ['jquery']},
		'foundation/foundation.abide': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.alerts': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.clearing': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.cookie': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.dropdown': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.forms': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.interchange': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.joyride': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.magellan': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.orbit': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.placeholder': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.reveal': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.section': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.tooltips': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.topbar': {deps: ['jquery', 'foundation/foundation'] },
	}
});

// Includes Desktop Specific JavaScript files here (or inside of your Desktop router)
require(['App', 'routers/AppRouter', 'controllers/Controller', 'jquery'],
	function(App, AppRouter, Controller)
	{
		App.appRouter = new AppRouter(
		{
			controller : new Controller()
		});
		App.start();
		window.App = App;
	});
