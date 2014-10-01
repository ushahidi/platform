/**
 * Tag Collection
 *
 * @module     TagCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'models/TagModel', 'modules/config', 'util/App.storage', 'backbone.paginator', 'mixin/FilteredCollection'],
	function(Backbone, _, TagModel, config, Storage, PageableCollection, FilteredCollection)
	{
		var page_size_storage = new Storage('Ushahidi', 'page_size_tags');

		// Creates a new Backbone Collection class object
		var TagCollection = PageableCollection.extend(
			_.extend(
			{
				model : TagModel,
				url: config.get('apiurl') +'tags',
				mode: 'client',

				// Set state params for `Backbone.PageableCollection#state`
				state: {
					firstPage: 0,
					currentPage: 0,
					pageSize: parseInt(page_size_storage.get()) || 20,
					// Required under server-mode
					totalRecords: 0,
					sortKey: 'created',
					order: 1 // 1 = desc
				},

				pageSizes: [20, 50, 100],

				sortKeys: {
					created : 'Date/Time created',
					id : 'ID',
					tag : 'Tag Name'
				},

				sortOrder: {
					tag : -1
				}
			},

			// Mixins must always be added last!
			FilteredCollection
		));

		return TagCollection;
	});
