/**
 * Layer Collection Module
 *
 * @module     LayerCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'models/LayerModel', 'modules/config', 'mixin/ResultsCollection'],
	function(Backbone, _, LayerModel, config, ResultsCollection)
	{
		var LayerCollection = Backbone.Collection.extend(
			_.extend(
			{
				model : LayerModel,
				url: config.get('apiurl') +'layers',
			},
			// Mixins must always be added last!
			ResultsCollection
		));

		return LayerCollection;
	});
