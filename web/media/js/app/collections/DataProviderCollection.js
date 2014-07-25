/**
 * Data Provider Collection Module
 *
 * @module     DataProviderCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'models/DataProviderModel', 'modules/config', 'mixin/ResultsCollection'],
	function(Backbone, _, DataProviderModel, config, ResultsCollection)
	{
		var DataProviderCollection = Backbone.Collection.extend(
			_.extend(
			{
				model : DataProviderModel,
				url: config.get('apiurl') + 'dataproviders'
			},
			// Mixins must always be added last!
			ResultsCollection
		));

		return DataProviderCollection;
	});
