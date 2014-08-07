/**
 * ReplyView
 *
 * @module     ReplyView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'underscore','hbs!messages/list/Reply'],
	function(Marionette, _, template)
	{
		return  Marionette.ItemView.extend(
		{
			//Template HTML string
			template: template,
			tagName: 'li',
			className: 'card-panel--activity-list__item',
			modelEvents: {
				'sync': 'render'
			},
			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					isIncoming : this.model.isIncoming(),
				});
				return data;
			},
		});
	});
