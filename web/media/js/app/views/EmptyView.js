/**
 * Provide a generic view for displaying a message when the list is empty.
 *
 *
 * @module     LoadingView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
define(['App', 'marionette', 'underscore', 'hbs!templates/Empty', 'i18next'],
	function(App, Marionette, _, template, i18n)
	{
		return Marionette.ItemView.extend(
		{
			template: template,
			tagName: 'li',
			className: 'list-view-empty',
			selected: false,

			/**
			 * @param  {object} options View options
			 *     modelName - model name index for looking up empty message in i18n
			 *     emptyMessage - empty message to show (this overrides modelName)
			 * @return View
			 */
			initialize: function(options)
			{
				options = _.defaults(options, {
					modelName : 'default'
				});

				// If emptyMessage isn't passed, get message from i18n
				if (! options.emptyMessage)
				{
					options.emptyMessage = i18n.t(['empty.'+options.modelName, 'empty.default']);
				}

				this.emptyMessage = options.emptyMessage;
			},
			serializeData: function()
			{
				var data = {
					message: this.emptyMessage
				};

				return data;
			},

		});
	});