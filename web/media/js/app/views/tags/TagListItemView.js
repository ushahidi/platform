/**
 * User List Item View
 *
 * @module     UserListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['views/ListItemView', 'hbs!templates/tags/TagListItem'],
	function(ListItemView, template)
	{
		return ListItemView.extend(
		{
			template: template,
			className: 'list-view-tag',
		});
	});
