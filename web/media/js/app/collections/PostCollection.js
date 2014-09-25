/**
 * Post Collection
 *
 * @module     PostCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'models/PostModel', 'modules/config', 'backbone.paginator', 'mixin/FilteredCollection'],
	function(Backbone, _, PostModel, config, PageableCollection, FilteredCollection)
	{
		// Creates a new Backbone Collection class object
		var PostCollection = PageableCollection.extend(
			_.extend(
			{
				model : PostModel,
				url: config.get('apiurl') +'posts',

				// Set state params for `Backbone.PageableCollection#state`
				state: {
					firstPage: 0,
					currentPage: 0,
					pageSize: 3,
					// Required under server-mode
					totalRecords: 0,
					sortKey: 'updated',
					order: 1 // 1 = desc
				},

				sortKeys: {
					updated : 'Date/Time updated',
					created : 'Date/Time created',
					title : 'Title A-Z',
					id : 'ID'
				},

				sortOrder: {
					title : -1
				},

				/**
				 * Get next model in collection
				 * @return PostModel|false
				 */
				getNextModel : function(model)
				{
					var next = this.at(this.indexOf(model) + 1);
					return (next || false);
				},

				/**
				 * Get previous model in collection
				 * @return PostModel|false
				 */
				getPrevModel : function(model)
				{
					var prev = this.at(this.indexOf(model) - 1);
					return (prev || false);
				}
			},

			// Mixins must always be added last!
			FilteredCollection
		));

		return PostCollection;
	});
