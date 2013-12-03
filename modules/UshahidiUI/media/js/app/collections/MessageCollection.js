/**
 * Message Collection
 *
 * @module     MessageCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'models/MessageModel', 'App', 'backbone-pageable'],
	function($, Backbone, MessageModel, App, PageableCollection)
	{
		// Creates a new Backbone Collection class object
		var MessageCollection = PageableCollection.extend(
		{
			model : MessageModel,
			url: App.config.baseurl + App.config.apiuri + '/messages',
			// The Ushahidi API returns models under 'results'.
			parseRecords: function(response)
			{
				return response.results;
			},
			parseState: function(response)
			{
				return {
					totalRecords: response.total_count
				};
			},
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

			// Mapping from a `Backbone.PageableCollection#state` key to the
			// query string parameters accepted by the Ushahidi API.
			queryParams: {
				currentPage: null,
				totalPages: null,
				totalRecords: null,
				pageSize: 'limit',
				offset: function () { return this.state.currentPage * this.state.pageSize; },
				sortKey: 'orderby'
			},

			sortKeys: {
				'created' : 'Date/Time created',
				'id' : 'ID'
			}
		});

		return MessageCollection;
	});
