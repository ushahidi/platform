/**
 * DateTime Value
 *
 * @module     LinkValue
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['views/values/Value', 'hbs!templates/values/DateTimeValue'],
	function(ValueView, template) {
		return ValueView.extend(
		{
			template : template
		});
	});