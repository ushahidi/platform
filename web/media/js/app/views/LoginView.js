/**
 * Login
 *
 * @module     LoginView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'marionette', 'hbs!templates/Login.html'],
	function( Marionette, template)
	{
		return Marionette.ItemView.extend( {
			template: template,
			initialize: function() { }
		});
	});
