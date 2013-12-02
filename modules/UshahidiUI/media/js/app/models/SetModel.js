/**
 * Set Model
 *
 * @module     SetModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App'],
	function($, Backbone, App) {
		var SetModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri + '/sets'
		});

		return SetModel;
	});