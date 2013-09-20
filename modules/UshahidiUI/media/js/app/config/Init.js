require.config(
{
	baseUrl : './media/kohana/js/app',
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
		'moment': {
			exports: 'moment'
		},
		
		'foundation/foundation' : {exports: 'Foundation'},
		'foundation/foundation.alerts': {deps: ['foundation/foundation'] },
		'foundation/foundation.clearing': {deps: ['foundation/foundation'] },
		'foundation/foundation.cookie': {deps: ['foundation/foundation'] },
		'foundation/foundation.dropdown': {deps: ['foundation/foundation'] },
		'foundation/foundation.forms': {deps: ['foundation/foundation'] },
		'foundation/foundation.interchange': {deps: ['foundation/foundation'] },
		'foundation/foundation.joyride': {deps: ['foundation/foundation'] },
		'foundation/foundation.magellan': {deps: ['foundation/foundation'] },
		'foundation/foundation.orbit': {deps: ['foundation/foundation'] },
		'foundation/foundation.placeholder': {deps: ['foundation/foundation'] },
		'foundation/foundation.reveal': {deps: ['foundation/foundation'] },
		'foundation/foundation.section': {deps: ['foundation/foundation'] },
		'foundation/foundation.tooltips': {deps: ['foundation/foundation'] },
		'foundation/foundation.topbar': {deps: ['foundation/foundation'] },
	}
});

// Includes Desktop Specific JavaScript files here (or inside of your Desktop router)
require(['App', 'routers/AppRouter', 'controllers/Controller', 'jquery', 'backbone.validateAll'],
	function(App, AppRouter, Controller)
	{
		App.appRouter = new AppRouter(
		{
			controller : new Controller()
		});
		App.start();
		window.App = App;
	});