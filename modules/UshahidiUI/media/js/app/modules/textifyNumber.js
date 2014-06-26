/**
 * Convert Number to Text Helper
 *
 * @module     textifyNumber
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([],
	function()
	{
		var powers = {
				billion:  Math.pow(10, 9),
				million:  Math.pow(10, 6),
				thousand: Math.pow(10, 3)
			},
			names = {
				billion:  'b',
				million:  'm',
				thousand: 'k'
			};
		return function(value) {
			for (var idx in powers) {
				if (value >= powers[idx]) {
					value = value / powers[idx];
					return Math.floor(value) + names[idx];
				}
			}
			return value;
		};
	});
