/**
 * User Collection Module
 *
 * @module     UserCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'underscore', 'modules/config', 'models/UserModel', 'backbone.paginator', 'mixin/FilteredCollection'],
	function(Backbone, _, config, UserModel, PageableCollection, FilteredCollection)
	{
		// Creates a new Backbone Collection class object
		var UserCollection = PageableCollection.extend(
			_.extend(
			{
				model : UserModel,
				url: config.get('apiurl') + 'users',

				// Set state params for `Backbone.PageableCollection#state`
				state: {
					firstPage: 0,
					currentPage: 0,
					pageSize: 3,
					// Required under server-mode
					totalRecords: 0,
					sortKey: 'created',
					order: 1 // 1 = desc
				},

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
