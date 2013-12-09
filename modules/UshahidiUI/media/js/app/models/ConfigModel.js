/**
 * Config Model
 *
 * @module     ConfigModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App'],
	function($, Backbone, App) {
		var ConfigModel = Backbone.Model.extend(
		{
			urlRoot: function() {
				var base = App.config.baseurl + 'api/v2/config';
				base = this.get('group_name') ? base + '/' + this.get('group_name') : base;
				return base;
			},
			idAttribute : 'config_key'
		});

		return ConfigModel;
	});