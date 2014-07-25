/**
 * Config Model
 *
 * @module     ConfigModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'backbone-model-factory'],
	function(Backbone) {
		var ConfigModel = Backbone.ModelFactory(
		{
			urlRoot: function() {
				// this has to be defined as a closure to prevent a circular dependency
				var config = require('modules/config');
				return config.get('apiurl') + 'config';
			},
			idAttribute : '@group'
		});

		return ConfigModel;
	});
