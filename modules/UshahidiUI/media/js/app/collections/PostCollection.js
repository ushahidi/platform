/**
 * Post Collection
 *
 * @module     PostCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'underscore', 'models/PostModel', 'App', 'backbone-pageable'],
	function($, Backbone, _, PostModel, App, PageableCollection)
	{
		// Creates a new Backbone Collection class object
		var PostCollection = PageableCollection.extend(
		{
			model : PostModel,
			url: App.config.baseurl + App.config.apiuri +'/posts',
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
				sortKey: 'updated',
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

			/**
			 * Get filter params to be sent to the server
			 * @return {Object} Filter params
			 */
			getFilterParams : function ()
			{
				var pagingParamKeys;

				// Grab the keys of the paging params so we don't overwrite these
				// and mess up the paging
				pagingParamKeys = _.union(
					_.keys(Object.getPrototypeOf(this).queryParams),
					_.keys(PageableCollection.prototype.queryParams)
				);

				return _.omit(this.queryParams, pagingParamKeys);
			},

			/**
			 * Set filter params to be sent to the server as query params
			 * @param  {Object}  filterParams     New parameters to filter by
			 * @param  {Boolean} replaceExisting  If true, filters replace the existing filter
			 *                                    If false, filters are added to existing filter
			 * @return {Object}                   Filter params set on the object
			 */
			setFilterParams : function (filterParams, replaceExisting)
			{
				var pagingParamKeys,
					pagingParams,
					oldFilterParams,
					newFilterParams;

				// Make sure filter params is an object
				filterParams = _.extend({}, filterParams);

				// Grab the keys of the paging params so we don't overwrite these
				// and mess up the paging
				pagingParamKeys = _.union(
					_.keys(Object.getPrototypeOf(this).queryParams),
					_.keys(PageableCollection.prototype.queryParams)
				);

				// Grab existing paging params
				pagingParams = _.pick(this.queryParams, pagingParamKeys);
				// Get old filter params
				oldFilterParams = _.omit(this.queryParams, pagingParamKeys);
				// Get filter params, excluded keys reserved for paging.
				newFilterParams = _.omit(filterParams, pagingParamKeys);

				// If we're not replacing all filters, merge the oldFilters in too
				if (! replaceExisting)
				{
					newFilterParams = _.extend({}, oldFilterParams, newFilterParams);
				}

				// Override query params with new filter params + paging params
				this.queryParams = _.extend({}, newFilterParams, pagingParams);

				// If filter has changed, reload
				if (! _.isEqual(oldFilterParams, newFilterParams))
				{
					this.trigger('filter:change');
					this.fetch();
				}

				return newFilterParams;
			}
		});

		return PostCollection;
	});