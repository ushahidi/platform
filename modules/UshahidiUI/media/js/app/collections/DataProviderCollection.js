/**
 * Data Provider Collection Module
 *
 * @module     DataProviderCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'models/DataProviderModel', 'App'],
	function($, Backbone, DataProviderModel, App)
	{
		var DataProviderCollection = Backbone.Collection.extend(
		{
			model : DataProviderModel,
			url: App.config.baseurl + App.config.apiuri +'/dataproviders',
			// The Ushahidi API returns models under 'results'.
			parse: function(response)
			{
				return response.results;
			}
		});

		return DataProviderCollection;
	});
