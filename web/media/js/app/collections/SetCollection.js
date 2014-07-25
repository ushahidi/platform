/**
 * Set Collection
 *
 * @module     SetCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'models/SetModel', 'modules/config', 'backbone.paginator', 'mixin/ResultsCollection', 'mixin/FilteredCollection'],
	function(Backbone, _, SetModel, config, PageableCollection, ResultsCollection, FilteredCollection)
	{
		// Creates a new Backbone Collection class object
		var SetCollection = PageableCollection.extend(
		_.extend(
			{
				model : SetModel,
				url: config.get('apiurl') + 'sets',
				// Set state params for `Backbone.PageableCollection#state`
				state: {
					firstPage: 0,
					currentPage: 0,
					pageSize: 3,
					// Required under server-mode
					totalRecords: 3,
					sortKey: 'created',
					order: 1 // 1 = desc
				},
			},

			// Mixins must always be added last!
			FilteredCollection
		));

		return SetCollection;
	});
