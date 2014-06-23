/**
 * Forms
 *
 * @module     Forms
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'underscore', 'hbs!templates/settings/FormEditor'],
	function(Marionette, _, template)
	{
		return Marionette.Layout.extend(
		{
			template: template,
			regions : {
				availableAttributes : '.available-attributes',
				formAttributes : '.form-attributes'
			}
		});
	});
