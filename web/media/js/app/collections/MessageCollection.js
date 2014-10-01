/**
 * Message Collection
 *
 * @module     MessageCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'backbone', 'models/MessageModel', 'modules/config', 'util/App.storage', 'backbone.paginator', 'mixin/FilteredCollection'],
	function(_, Backbone, MessageModel, config, Storage, PageableCollection, FilteredCollection)
	{
		var page_size_storage = new Storage('Ushahidi', 'page_size_messages');

		// Creates a new Backbone Collection class object
		var MessageCollection = PageableCollection.extend(
			_.extend(
			{
				model : MessageModel,
				url: config.get('apiurl') + 'messages',

				// Set state params for `Backbone.PageableCollection#state`
				state: {
					firstPage: 0,
					currentPage: 0,
					pageSize: parseInt(page_size_storage.get()) || 10,
					// Required under server-mode
					totalRecords: 0,
					sortKey: 'created',
					order: 1 // 1 = desc
				},

				pageSizes: [10, 20, 50],

				sortKeys: {
					created : 'Date/Time created',
					id : 'ID'
				},

				sourceTypes: {
					email : 'Email',
					sms : 'SMS',
					twitter : 'Twitter'
				},

				boxTypes: {
					inbox : 'Inbox',
					outbox : 'Outbox',
					archived : 'Archived'
				}
			},

			// Mixins must always be added last!
			FilteredCollection
		));

		return MessageCollection;
	});
