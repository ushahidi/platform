/**
 * Provide a generic view for displaying a message when the list is empty.
 *
 *
 * @module     LoadingView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
define(['App','handlebars', 'marionette', 'text!templates/Loading.html'],
	function(App,Handlebars, Marionette, template)
	{
		return Marionette.ItemView.extend(
		{
			//TODO:: Figure out how to make use of this view as both loading indicator and to display message for an empty list.
			// Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-user',

			initialize: function(options)
			{
				var defaultOptions = {
					emptyMessage : "Empty List"
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