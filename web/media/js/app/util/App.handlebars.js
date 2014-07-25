/**
 * Handlebars Helpers
 *
 * @module     App.handlebars
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['handlebars', 'underscore', 'moment', 'modules/config', 'underscore.string', 'handlebars-paginate', 'hbs!templates/partials/pagination', 'hbs!templates/partials/list-info', 'hbs!templates/partials/tag-with-icon'],
	function(Handlebars, _, moment, config, _str, paginate, paginationTpl, listInfoTpl, tagWithIconTpl)
	{
		Handlebars.registerHelper('url', function(options)
		{
			var url,
				App = require ('App'),
				baseurl = config.get('basepath');

			// Has this helper been used directly or as a block helper?
			if (typeof options === 'string')
			{
				url = options;
			}
			else
			{
				url = options.fn(this);
			}

			// If pushstate is disabled, add #! to urls
			if (! App.feature('pushstate'))
			{
				baseurl += '#';
			}

			return baseurl + url;
		});

		Handlebars.registerHelper('imageurl', function(url)
		{
			return config.get('imagedir') + url;
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

		Handlebars.registerHelper('feature', function (feature, options)
		{
			var App = require ('App');
			return App.feature(feature) ? options.fn(this) : '';
		});

		/**
		 * Return an <option> tag with value, label and selected attribute
		 */
		Handlebars.registerHelper('option', function(value, label, selectedValue) {
			var selectedProperty;
			if (_.isArray(selectedValue))
			{
				selectedProperty = (_.indexOf(selectedValue, value) >= 0) ? 'selected="selected"' : '';
			}
			else
			{
				selectedProperty = (value === selectedValue) ? 'selected="selected"' : '';
			}

			return new Handlebars.SafeString(
				'<option value="' + Handlebars.Utils.escapeExpression(value) + '"' + selectedProperty + '>' +
				Handlebars.Utils.escapeExpression(label) +
				'</option>'
			);
		});

		Handlebars.registerPartial('pagination', paginationTpl);
		Handlebars.registerPartial('listinfo', listInfoTpl);
		Handlebars.registerPartial('tag-with-icon', tagWithIconTpl);

		return Handlebars;
	});
