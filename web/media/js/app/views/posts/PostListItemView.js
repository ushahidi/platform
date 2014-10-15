/**
 * Post List Item
 *
 * @module     PostItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'views/posts/PostItemView', 'hbs!templates/posts/PostListItem'],
	function(_, PostItemView, template)
	{
		return PostItemView.extend(
		{
			template: template,
			tagName: 'li',
			className: 'list-view-post',

			behaviors: {
				SelectableListItem: {}
			},
			// Override serializeData to include value of 'selected'
			serializeData: function()
			{
				var data = _.extend(
					PostItemView.prototype.serializeData.call(this),
					{
						selected : this.selected
					}
				);
				return data;
			},
		});
	});
