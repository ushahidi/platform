/**
 * User List Item View
 *
 * @module     UserListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'util/notify', 'hbs!templates/tags/TagListItem'],
	function(App, Marionette, notify, template)
	{
		return Marionette.ItemView.extend(
		{
			template: template,
			tagName: 'li',
			className: 'list-view-tag',

			events: {
				'click .js-tag-delete': 'deleteTag',
				'click .js-tag-edit' : 'showEditTag'
			},

			modelEvents: {
				'sync': 'render'
			},

			behaviors: {
				SelectableListItem: {}
			},

			deleteTag: function(e)
			{
				e.preventDefault();
				notify.destroy(this.model, 'tag');
			},

			showEditTag : function (e)
			{
				e.preventDefault();
				App.vent.trigger('tag:edit', this.model);
			}
		});
	});
