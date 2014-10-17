/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([
		'views/ListView',
		'views/tags/TagListItemView',
		'hbs!templates/tags/TagList'
	],
	function(
		ListView,
		TagListItemView,
		template
	)
	{
		return ListView.extend(
		{
			template: template,
			modelName: 'tag',

			childView: TagListItemView,

			behaviors: {
				PageableView: {
					modelName: 'tags'
				}
			},

		});
	});
