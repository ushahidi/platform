/**
 * Provide a generic view for displaying a message when the list is empty.
 *
 *
 * @module     LoadingView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
define(['App', 'marionette', 'underscore', 'hbs!templates/Empty'],
	function(App, Marionette, _, template)
	{
		return Marionette.ItemView.extend(
		{
			//TODO:: Figure out how to make use of this view as both loading indicator and to display message for an empty list.
			template: template,
			tagName: 'li',
			className: 'list-view-empty',
			selected: false,

			initialize: function(options)
			{
				var defaultOptions = {
					emptyMessage : 'No records found.'
				};

				options = _.defaults(options,defaultOptions);
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