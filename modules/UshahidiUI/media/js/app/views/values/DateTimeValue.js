/**
 * DateTime Value
 *
 * @module     LinkValue
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['handlebars', 'views/values/Value', 'text!templates/values/DateTimeValue.html'],
	function(Handlebars, ValueView, template) {
		return ValueView.extend(
		{
			template : Handlebars.compile(template)
		});
	});