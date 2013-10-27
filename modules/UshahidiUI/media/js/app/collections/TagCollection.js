/**
 * Tag Collection
 *
 * @module     TagCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'models/TagModel', 'App'],
	function($, Backbone, TagModel, App)
	{
		// Creates a new Backbone Collection class object
		var TagCollection = Backbone.Collection.extend(
		{
			model : TagModel,
			url: App.config.baseurl + 'api/v2/tags',
			// The Ushahidi API returns models under 'results'.
			parse: function(response)
			{
				return response.results;
			}
		});
	
		return TagCollection;
	});