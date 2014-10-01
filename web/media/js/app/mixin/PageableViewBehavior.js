define(['marionette', 'handlebars'], function (Marionette, Handlebars) {
	var PageableView = Marionette.Behavior.extend({
		ui : {
			pageFirst : '.js-page-first',
			pageNext : '.js-page-next',
			pagePrev : '.js-page-prev',
			pageLast : '.js-page-last',
			pageChange : '.js-page-change',
			pageCount : '.js-filter-count',
			pageSort : '.js-filter-sort',
			pagination : '.js-pagination',
			listViewInfo : '.js-list-view-filter-info'
		},

		events : {
			'click @ui.pageFirst' : 'showFirstPage',
			'click @ui.pageNext' : 'showNextPage',
			'click @ui.pagePrev' : 'showPreviousPage',
			'click @ui.pageLast' : 'showLastPage',
			'click @ui.pageChange' : 'showPage',
			'change @ui.pageCount' : 'updatePageSize',
			'change @ui.pageSort' : 'updateSort'
		},

		collectionEvents :
		{
			reset : 'updatePagination',
			add : 'updatePagination',
			remove : 'updatePagination'
		},

		showNextPage : function (e)
		{
			e.preventDefault();
			// Check if we're already at the last page.
			if (this.view.collection.state.lastPage > this.view.collection.state.currentPage)
			{
				this.view.collection.getNextPage();
				this.updatePagination();
			}
		},
		showPreviousPage : function (e)
		{
			e.preventDefault();
			// Check if we're already at the first page.
			if (this.view.collection.state.firstPage < this.view.collection.state.currentPage)
			{
				this.view.collection.getPreviousPage();
				this.updatePagination();
			}
		},
		showFirstPage : function (e)
		{
			e.preventDefault();
			// Check if we're already at the first page
			if (this.view.collection.state.firstPage < this.view.collection.state.currentPage)
			{
				this.view.collection.getFirstPage();
				this.updatePagination();
			}
		},
		showLastPage : function (e)
		{
			e.preventDefault();
			// Check if we're already at the last page
			if (this.view.collection.state.lastPage > this.view.collection.state.currentPage)
			{
				this.view.collection.getLastPage();
				this.updatePagination();
			}
		},
		showPage : function (e)
		{
			var $el = this.$(e.currentTarget),
					num = $el.data('page') - 1;

			e.preventDefault();

			this.view.collection.getPage(num);
			this.updatePagination();
		},

		updatePagination: function ()
		{
			// @todo replace with ui.pagination, but currently broken by .replaceWith()
			this.view.$('.js-pagination').replaceWith(
				Handlebars.partials.pagination({
					pagination: this.view.collection.state
				})
			);
			// @todo replace with ui.listViewInfo, but currently broken by .replaceWith()
			this.view.$('.js-list-view-filter-info').replaceWith(
				Handlebars.partials.listinfo({
					pagination: this.view.collection.state,
					modelName: this.options.modelName
				})
			);

			// Update counter
			this.view.$('li.active span.js-tab-number-label').text(this.view.collection.state.totalRecords);
		},
		updatePageSize : function (e)
		{
			e.preventDefault();
			var size = parseInt(this.ui.pageCount.val(), 10);
			if (size > 0)
			{
				this.view.collection.setPageSize(size, {
					first: true
				});
			}
		},
		updateSort : function (e)
		{
			e.preventDefault();
			var sortkey = this.ui.pageSort.val(),
				order = (typeof this.view.collection.sortOrder[sortkey] !== 'undefined') ? this.view.collection.sortOrder[sortkey] : 1;

			this.view.collection.setSorting(sortkey, order);

			if (this.view.collection.mode === 'client')
			{
				this.view.collection.fullCollection.sort();
			}
			this.view.collection.getFirstPage();
		}
	});

	return PageableView;
});