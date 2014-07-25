/**
 * Role Model
 *
 * @module     RoleModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'modules/config', 'backbone-model-factory'],
	function(Backbone, config) {
		var RoleModel = Backbone.ModelFactory(
		{
			urlRoot: config.get('apiurl') + 'roles',
			idAttribute : 'name',
			toString : function ()
			{
				return this.get('display_name');
			},
		});

		return RoleModel;
	});
