/**
 * Form List Item View
 *
 * @module     FormListItem
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App','handlebars', 'marionette', 'text!templates/settings/FormListItem.html'],
	function(App,Handlebars, Marionette, template)
	{
		return Marionette.ItemView.extend(
		{
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-form',

			modelEvents: {
				'sync': 'render',
				'change': 'render'
			}
		});
	});
