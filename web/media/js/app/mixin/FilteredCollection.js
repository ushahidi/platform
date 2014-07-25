/**
 * Filtered Collection mixin
 *
 * @module     App.mixin
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'backbone.paginator'],
	function(_, PageableCollection)
	{
		// Default filtering setup for collections.

		return {
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
					// .getFirstPage() will trigger a fetch() but also resets to the first page
					// avoiding weird results when the new result set fewer pages than the current page
					this.getFirstPage();
				}

				return newFilterParams;
			},

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
			}
		};
	});
