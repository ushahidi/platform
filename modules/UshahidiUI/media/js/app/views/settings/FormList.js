/**
 * Form List
 *
 * @module     FormListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'underscore', 'views/settings/FormListItem', 'views/EmptyView', 'text!templates/settings/FormList.html'],
	function( App, Marionette, Handlebars, _, FormListItem, EmptyView, template)
	{
		return Marionette.CompositeView.extend(
		{
			template: Handlebars.compile(template),

			itemView: FormListItem,

			itemViewContainer: '.list-view-form-list',

			itemViewOptions:
			{
				emptyMessage: 'No forms found.',
			},

			emptyView: EmptyView,

		});
	});