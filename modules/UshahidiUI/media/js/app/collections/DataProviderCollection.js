/**
 * Data Provider Collection Module
 *
 * @module     DataProviderCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'models/DataProviderModel', 'modules/config'],
	function(Backbone, DataProviderModel, config)
	{
		var DataProviderCollection = Backbone.Collection.extend(
		{
			model : DataProviderModel,
			url: config.get('apiurl') + '/dataproviders',
			// The Ushahidi API returns models under 'results'.
			parse: function(response)
			{
				return response.results;
			}
		});

		return DataProviderCollection;
	});
