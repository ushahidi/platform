/**
 * User Model
 *
 * @module     UserModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'App'],
	function(Backbone, App) {
		var UserModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + 'api/v2/users'
		});
		return UserModel;
	});