define(['handlebars', 'moment', 'underscore.string', 'handlebars-paginate'],
	function(Handlebars, moment, _str, paginate)
	{
		Handlebars.registerHelper('baseurl', function()
		{
			var App = require ('App');
			return App.config.baseurl;
		});
		
		Handlebars.registerHelper('url', function(url)
		{
			var App = require ('App');
			return App.config.baseurl  + url;
		});
		
		Handlebars.registerHelper('imageurl', function(url)
		{
			var App = require ('App');
			return App.config.baseurl + App.config.imagedir +  '/' + url;
		});
		
		Handlebars.registerHelper('datetime-fromNow', function(timestamp)
		{
			return moment(timestamp).fromNow();
		});
		
		Handlebars.registerHelper('datetime-calendar', function(timestamp)
		{
			return moment(timestamp).calendar();
		});
		
		Handlebars.registerHelper('datetime', function(timestamp)
		{
			return moment(timestamp).format('LLL');
		});
		
		Handlebars.registerHelper('prune', function(text, length)
		{
			return _str.prune(text, length);
		});
		
		Handlebars.registerHelper('paginate', paginate);

		return Handlebars;
	});