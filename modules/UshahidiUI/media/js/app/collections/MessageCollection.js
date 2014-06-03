/**
 * Message Collection
 *
 * @module     MessageCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'underscore', 'backbone', 'models/MessageModel', 'App', 'backbone.paginator', 'mixin/ResultsCollection', 'mixin/FilteredCollection'],
	function($, _, Backbone, MessageModel, App, PageableCollection, ResultsCollection, FilteredCollection)
	{
		// Creates a new Backbone Collection class object
		var MessageCollection = PageableCollection.extend(
			_.extend(
			{
				model : MessageModel,
				url: App.config.baseurl + App.config.apiuri + '/messages',

				// Set state params for `Backbone.PageableCollection#state`
				state: {
					firstPage: 0,
					currentPage: 0,
					pageSize: 4,
					// Required under server-mode
					totalRecords: 0,
					sortKey: 'created',
					order: 1 // 1 = desc
				},

				sortKeys: {
					'created' : 'Date/Time created',
					'id' : 'ID'
				},

				sourceTypes: {
					'email' : 'Email',
					'sms' : 'SMS',
					'twitter' : 'Twitter'
				},

				boxTypes: {
					'inbox' : 'Inbox',
					'outbox' : 'Outbox',
					'archived' : 'Archived'
				}
			},

			// Mixins must always be added last!
			ResultsCollection,
			FilteredCollection
		));

		return MessageCollection;
	});
