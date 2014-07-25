/**
 * Set List Item
 *
 * @module     SetItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'hbs!sets/SetListItem'],
	function(Marionette, template)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: template,
			tagName: 'li',
			className: 'list-view-message'
		});
	});