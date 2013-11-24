/**
 * Post List Item
 *
 * @module     PostItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['views/PostItemView', 'text!templates/PostListItem.html'],
	function(PostItemView, template)
	{
		//ItemView provides some default rendering logic
		return PostItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post'
		});
	});
