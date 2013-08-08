define(['handlebars', 'moment', 'underscore.string'],
	function(Handlebars, moment, _str) {
		Handlebars.registerHelper('baseurl', function() {
			var App = require ('App');
			return App.config.baseurl;
		});
		
		Handlebars.registerHelper('url', function(url) {
			var App = require ('App');
			return App.config.baseurl  + url;
		});
		
		Handlebars.registerHelper('imageurl', function(url) {
			var App = require ('App');
			return App.config.baseurl + App.config.imagedir +  '/' + url;
		});
		
		Handlebars.registerHelper('datetime-fromNow', function(timestamp) {
			return moment(timestamp).fromNow();
		});
		
		Handlebars.registerHelper('datetime-calendar', function(timestamp) {
			return moment(timestamp).calendar();
		});
		
		Handlebars.registerHelper('datetime', function(timestamp) {
			return moment(timestamp).format('LLL');
		});
		
		_.str = require('underscore.string');
		Handlebars.registerHelper('prune', function(text, length) {
			
			return _str.prune(text, length);
		});
		
		return Handlebars;
	}); 