/**
 * Tag Collection
 *
 * @module     TagCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'modules/config', 'models/MediaModel', 'mixin/ResultsCollection'],
	function(Backbone, _, config, MediaModel, ResultsCollection)
	{
		// Creates a new Backbone Collection class object
		var MediaCollection = Backbone.Collection.extend(
			_.extend(
			{
				model : MediaModel,
				url: config.get('apiurl') +'media',
			},
			// Mixins must always be added last!
			ResultsCollection
		));

		return MediaCollection;
	});
