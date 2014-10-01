/**
 * User Collection Module
 *
 * @module     UserCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'modules/config', 'util/App.storage', 'models/UserModel', 'backbone.paginator', 'mixin/FilteredCollection'],
	function(Backbone, _, config, Storage, UserModel, PageableCollection, FilteredCollection)
	{
		var page_size_storage = new Storage('Ushahidi', 'page_size_users'),
			UserCollection = PageableCollection.extend(
			// Creates a new Backbone Collection class object
			_.extend(
			{
				model : UserModel,
				url: config.get('apiurl') + 'users',

				// Set state params for `Backbone.PageableCollection#state`
				state: {
					firstPage: 0,
					currentPage: 0,
					pageSize: parseInt(page_size_storage.get(), 10) || 20,
					// Required under server-mode
					totalRecords: 0,
					sortKey: 'created',
					order: 1 // 1 = desc
				},

				pageSizes: [20, 50, 100],

				sortKeys: {
					created : 'Date/Time created',
					username : 'Username A-Z',
					id : 'ID'
				},

				sortOrder: {
					username : -1
				}
			},

			// Mixins must always be added last!
			FilteredCollection
		));

		return UserCollection;
	});
