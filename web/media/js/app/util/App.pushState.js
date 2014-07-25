/**
 * Push State utils
 *
 * @module     App.pushState
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['modules/config', 'jquery'],
	function(config, $)
	{
		/**
		 * Handle captured link click events
		 * @param  {[type]} event [description]
		 */
		var captureLinkClick = function(event)
		{
			var config = require('modules/config'),
				App = require('App'),
				href = $(event.currentTarget).attr('href'),
				passThrough = href.match(new RegExp('^/(user|oauth|api|media)')),
				url;

			// Allow shift+click for new tabs, etc.
			if (!passThrough && !event.altKey && !event.ctrlKey && !event.metaKey && !event.shiftKey)
			{
				event.preventDefault();

				// Remove leading slashes
				url = href.replace(new RegExp('^'+config.get('basepath')), '');
				// Instruct Backbone to trigger routing events
				App.appRouter.navigate(url, { trigger: true });

				return false;
			}
		},
		pushStateInit = function()
		{
			// Globally capture clicks. If they are internal and not in the pass
			// through list, route them through Backbone's navigate method.
			$(document).on('click', 'a[href^="' + config.get('basepath') + '"]', captureLinkClick);
		};

		return pushStateInit;
	}
);