/**
 * List Item View
 *
 * @module     ListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'util/notify'],
	function(App, Marionette, notify)
	{
		return Marionette.ItemView.extend(
		{
			tagName: 'li',
			className: 'list-view-item',

			initialize: function(options)
			{
				this.modelName = options.modelName;
			},

			events: {
				'click .js-delete': 'delete',
				'click .js-edit' : 'showEdit'
			},

			triggers:
			{
				'click .js-edit' : 'resource:edit'
			},

			modelEvents: {
				'sync': 'render'
			},

			behaviors: {
				SelectableListItem: {}
			},

			delete: function(e)
			{
				e.preventDefault();
				notify.destroy(this.model, this.modelName);
			}
		});
	});
