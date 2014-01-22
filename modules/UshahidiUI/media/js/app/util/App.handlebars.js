/**
 * Handlebars Helpers
 *
 * @module     App.handlebars
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

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

		/**
		 * Based on newLineToBR here: https://github.com/elving/swag/blob/master/lib/swag.js
		 **/
		Handlebars.registerHelper('newLineToBr', function(options)
		{
			var str;

			// Has this helper been used directly or as a block helper?
			if (typeof options === 'string')
			{
				str = Handlebars.Utils.escapeExpression(options);
			}
			else
			{
				str = options.fn(this);
			}

			return new Handlebars.SafeString(str.replace(/\r?\n|\r/g, '<br>'));
		});

		return Handlebars;
	});