/**
 * Attribute List
 *
 * @module     FormListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'views/settings/DraggableAttributeListItem', 'views/EmptyView'],
	function( Marionette, DraggableAttributeListItem, EmptyView)
	{
		return Marionette.CollectionView.extend(
		{
			tagName: 'ul',

			itemView: DraggableAttributeListItem,

			itemViewOptions:
			{
				emptyMessage: 'No forms found.',
			},

			emptyView: EmptyView,

			initialize: function (options)
			{
				this.itemViewOptions.sortableList = options.sortableList;
			}
		});
	});
