/**
 * User List Item
 *
 * @module     UserItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['handlebars', 'views/UserItemView', 'text!templates/UserListItem.html'],
	function(Handlebars, UserItemView, template)
	{
		//ItemView provides some default rendering logic
		return UserItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post'
		});
	});
