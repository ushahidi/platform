/**
 * Media Model
 *
 * @module     MediaModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'App'],
	function(Backbone, App)
	{
		var MediaModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri + '/media',
			validate : function(/*attrs, options*/) {
				if (this.isNew()) {
					// block validating of media models, require usage of ie Dropzone
					// to do POST file uploads directly. the API does not support
					// creating media via JSON at this time.
					return 'Media cannot be created using this interface';
				}
			}
		});
		return MediaModel;
	});

