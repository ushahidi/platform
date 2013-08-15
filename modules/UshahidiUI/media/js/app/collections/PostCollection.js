define(["jquery", "backbone", "models/PostModel", "App", "backbone-pageable"],
	function($, Backbone, PostModel, App, PageableCollection) {
		// Creates a new Backbone Collection class object
		//var PostCollection = Backbone.Collection.extend(
		var PostCollection = Backbone.PageableCollection.extend(
		{
			model : PostModel,
			url: App.config.baseurl + "api/v2/posts",
			// The Ushahidi API returns models under "results".
			parseRecords: function(response) {
				return response.results;
			},
			parseState: function(response) {
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
				pageSize: "limit",
				offset: function () { return this.state.currentPage * this.state.pageSize },
				sortKey: 'orderby'
			}
		});
	
		return PostCollection;
	}); 