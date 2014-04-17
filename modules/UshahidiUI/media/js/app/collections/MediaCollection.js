/**
 * Tag Collection
 *
 * @module     TagCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App', 'models/MediaModel'],
	function($, Backbone, App, MediaModel)
	{
		// Creates a new Backbone Collection class object
		var MediaCollection = Backbone.Collection.extend(
		{
			model : MediaModel,
			url: App.config.baseurl + App.config.apiuri +'/media',

			// The Ushahidi API returns models under 'results'.
			parse: function(response)
			{
				return response.results;
			}
		});

		return MediaCollection;
	});