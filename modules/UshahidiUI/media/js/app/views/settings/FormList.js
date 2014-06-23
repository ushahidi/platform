/**
 * Form List
 *
 * @module     FormListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'views/settings/FormListItem', 'views/EmptyView', 'hbs!templates/settings/FormList'],
	function( App, Marionette, _, FormListItem, EmptyView, template)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,

			itemView: FormListItem,

			itemViewContainer: '.list-view-form-list',

			itemViewOptions:
			{
				emptyMessage: 'No forms found.',
			},

			emptyView: EmptyView,

		});
	});