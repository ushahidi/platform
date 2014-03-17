/**
 * Role Collection Module
 *
 * @module     RoleCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'models/RoleModel', 'App'],
	function($, Backbone, RoleModel, App)
	{
		// Creates a new Backbone Collection class object
		var RoleCollection = Backbone.Collection.extend(
		{
			model : RoleModel,
			url: App.config.baseurl + App.config.apiuri +'/roles',
			// The Ushahidi API returns models under 'results'.
			parse: function(response)
			{
				return response.results;
			}
		});

		return RoleCollection;
	});