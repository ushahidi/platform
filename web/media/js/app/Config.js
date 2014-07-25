/**
 * Ushahidi RequireJS Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

require.config(
{
	// 3rd party script alias names (Easier to type 'jquery' than 'libs/jquery, etc')
	// probably a good idea to keep version numbers in the file names for updates checking
	paths :
	{
		'jquery' : '../../bower_components/jquery/dist/jquery',
		'jquery.cookie' : '../../bower_components/jquery.cookie/jquery.cookie',
		'simplepicker' : '../../bower_components/jquery-simplepicker/jquery.simplepicker',
		'underscore' : '../../bower_components/lodash/dist/lodash',
		'backbone' : '../../bower_components/backbone/backbone',
		'marionette' : '../../bower_components/backbone.marionette/lib/core/amd/backbone.marionette',
		'backbone.babysitter' : '../../bower_components/backbone.babysitter/lib/backbone.babysitter',
		'backbone.wreqr' : '../../bower_components/backbone.wreqr/lib/backbone.wreqr',
		'handlebars' : '../../bower_components/handlebars/handlebars',
		'leaflet' : '../../bower_components/leaflet/leaflet',
		'l.geosearch' : '../../bower_components/L.GeoSearch/src/js',
		'leaflet-locatecontrol' : '../../bower_components/leaflet-locatecontrol/src/L.Control.Locate',
		'l.markercluster' : '../../bower_components/leaflet.markercluster/dist/leaflet.markercluster',
		'moment' : '../../bower_components/moment/moment',
		'ddt' : '../../bower_components/ddt/ddt',
		'underscore.string' : '../../bower_components/underscore.string/lib/underscore.string',
		'foundation' : '../../bower_components/foundation/js/foundation',
		'foundation-loader' : '../libs/foundation-loader',
		'backbone.validateAll' : '../../bower_components/Backbone.validateAll/src/javascripts/Backbone.validateAll',
		'backbone.paginator' : '../../bower_components/backbone.paginator/lib/backbone.paginator',
		'handlebars-paginate' : '../libs/handlebars-paginate',
		'backbone-forms' : '../../bower_components/backbone-forms/distribution.amd/backbone-forms',
		'bf' : '../../bower_components/backbone-forms/distribution.amd/',
		'backbone-validation' : '../../bower_components/backbone.validation/dist/backbone-validation-amd',
		'backbone-model-factory' : '../../bower_components/backbone-model-factory/backbone-model-factory',
		'alertify' : '../../bower_components/alertify/alertify',
		'text' : '../../bower_components/requirejs-text/text',
		'dropzone' : '../../bower_components/dropzone/downloads/dropzone-amd-module',
		'syntaxhighlightjson' : '../libs/syntaxHighlightJson',
		'geocoder' : '../../bower_components/geocoder-js/dist/geocoder',
		'geopoint' : '../../bower_components/node-geopoint/geopoint',
		'datetimepicker' : '../../bower_components/datetimepicker/jquery.datetimepicker',
		'select2' : '../../bower_components/select2/select2',
		'jquery.nouislider' : '../../bower_components/nouislider/jquery.nouislider',
		'Link' : '../../bower_components/nouislider/Link',
		'jqueryui' : '../../bower_components/jquery.ui/ui',
		'URI' : '../../bower_components/URIjs/src/URI',
		// Deps for URI
		'punycode' : '../../bower_components/URIjs/src/punycode',
		'IPv6' : '../../bower_components/URIjs/src/IPv6',
		'SecondLevelDomains' : '../../bower_components/URIjs/src/SecondLevelDomains'
	},
	// Sets the configuration for your third party scripts that are not AMD compatible
	shim :
	{
		'handlebars' :
		{
			'exports' : 'Handlebars'
		},

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

		'l.markercluster': {
			deps: ['leaflet'],
			exports: 'L'
		},

		'ddt': {
			exports: 'ddt'
		},

		'syntaxhighlightjson' : {
			deps: ['jquery'],
			exports: 'syntaxHighlight'
		},

		'simplepicker' : {
			deps: ['jquery'],
		},

		'geopoint' :
		{
			'exports' : 'GeoPoint'
		},

		'datetimepicker' : {
			deps: ['jquery'],
		},

		'select2' : {
			deps: ['jquery'],
		},

		'Link' : {
			deps: ['jquery']
		},

		'jquery.nouislider' : {
			deps: ['jquery', 'Link']
		},

		'foundation/foundation' : {deps: ['jquery'], exports: 'Foundation'},
		'foundation/foundation.abide': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.accordion': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.alert': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.clearing': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.dropdown': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.equalizer': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.interchange': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.joyride': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.magellan': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.offcanvas': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.orbit': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.reveal': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.slider': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.tab': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.tooltip': {deps: ['jquery', 'foundation/foundation'] },
		'foundation/foundation.topbar': {deps: ['jquery', 'foundation/foundation'] }
	},
	hbs: {
		templateExtension: '.html'
	},
	packages: [
		{
			name: 'hbs',
			location: '../../bower_components/requirejs-hbs',
			main: 'hbs'
		}
	]
});

// This has to be outside the first require.config() call otherwise it breaks optimized builds
require.config({
	// Set baseurl based on config
	baseUrl : (window.config && window.config.jsdir) ? window.config.jsdir + '/app' : './media/js/app',
});
