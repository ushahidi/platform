/**
 * Set Collection
 *
 * @module     SetCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'models/SetModel', 'App', 'backbone-pageable'],
	function($, Backbone, SetModel, App, PageableCollection)
	{
		// Creates a new Backbone Collection class object
		var SetCollection = PageableCollection.extend(
		{
			model : SetModel,
			url: App.config.baseurl + App.config.apiuri + '/sets',
			// The Ushahidi API returns models under 'results'.
			parseRecords: function(response)
			{
				return response.results;
			},
			parseState: function(response)
			{
				return {
					totalRecords: response.count
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

		});

		return SetCollection;
	});