/**
 * Set Model
 *
 * @module     SetModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App', 'underscore', 'models/UserModel', 'models/FormModel'],
	function($, Backbone, App, _, UserModel, FormModel) {
		var PostModel = Backbone.Model.extend(
		{
			urlRoot: App.config.apiurl + 'sets',
		});
	});