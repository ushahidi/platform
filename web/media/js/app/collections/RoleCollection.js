/**
 * Role Collection Module
 *
 * @module     RoleCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'models/RoleModel', 'modules/config', 'mixin/ResultsCollection'],
	function(Backbone, _, RoleModel, config, ResultsCollection)
	{
		var RoleCollection = Backbone.Collection.extend(
			_.extend(
			{
				model : RoleModel,
				url: config.get('apiurl') +'roles',
			},
			// Mixins must always be added last!
			ResultsCollection
		));

		return RoleCollection;
	});
