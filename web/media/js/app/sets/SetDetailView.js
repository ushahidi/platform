/**
 * Set Detail
 *
 * @module     SetDetailView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'App', 'marionette', 'underscore', 'hbs!sets/SetDetail'],
    function( App, Marionette, _, template)
	{
		return Marionette.ItemView.extend( {
			template: template,
			initialize: function() {
			},
			events : {
			},
			serializeData: function() {
				return {
					pageSizes: {
						'20': '20',
						'50': '50',
						'100': '100'
					}
				};
			}
		});
	});
