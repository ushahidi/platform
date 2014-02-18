/**
 * Post List View
 *
 * @module     PostListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars','underscore', 'alertify', 'views/PostListItemView',
		'text!templates/PostList.html', 'text!templates/partials/pagination.html', 'text!templates/partials/post-list-info.html'],
	function( App, Marionette, Handlebars, _, alertify, PostListItemView,
		template, paginationTemplate, postListInfoTemplate)
	{
		Handlebars.registerPartial('pagination', paginationTemplate);
		Handlebars.registerPartial('post-list-info', postListInfoTemplate);

		return Marionette.CompositeView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			// Lets just store the partial templates somewhere usefule
			partialTemplates :
			{
				pagination : Handlebars.compile(paginationTemplate),
				postListInfo : Handlebars.compile(postListInfoTemplate)
			},
			initialize: function()
			{
				// Bind select/unselect events from itemviews
				this.on('itemview:select', this.showHideBulkActions, this);
				this.on('itemview:unselect', this.showHideBulkActions, this);
			},

			itemView: PostListItemView,
			itemViewOptions: {},

			itemViewContainer: '.list-view-posts-list',

			events:
			{
				'click .js-page-first' : 'showFirstPage',
				'click .js-page-next' : 'showNextPage',
				'click .js-page-prev' : 'showPreviousPage',
				'click .js-page-last' : 'showLastPage',
				'click .js-page-change' : 'showPage',
				'change #filter-posts-count' : 'updatePageSize',
				'change #filter-posts-sort' : 'updatePostsSort',
				'click .js-post-bulk-publish' : 'bulkPublish',
				'click .js-post-bulk-unpublish' : 'bulkUnpublish',
				'click .js-post-bulk-delete' : 'bulkDelete',
				'change .js-post-select-all' : 'selectAll'
			},

			collectionEvents :
			{
				reset : 'updatePagination',
				add : 'updatePagination',
				remove : 'updatePagination'
			},

			/**
			 * Get select child views
			 */
			getSelected : function ()
			{
				return this.children.filter('selected');
			},

			/**
			 * Show / Hide bulk actions toolbar when posts are selected
			 */
			showHideBulkActions : function ()
			{
				var selected = this.getSelected();

				if (selected.length > 0)
				{
					this.$('.js-list-view-bulk-actions').removeClass('visually-hidden');
					this.$('.js-list-view-bulk-actions').addClass('visible');
				}
				else
				{
					this.$('.js-list-view-bulk-actions').removeClass('visible');
					this.$('.js-list-view-bulk-actions').addClass('visually-hidden');
				}
			},

			/**
			 * Bulk publish selected posts
			 */
			bulkPublish : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				_.each(selected, function(item) {
					var model = item.model;
					model.set('status', 'published').save()
						.done(function()
						{
							alertify.success('Post has been published');
						}).fail(function ()
						{
							alertify.error('Unable to publish post, please try again');
						});
				} );
			},

			/**
			 * Bulk unpublish selected posts
			 */
			bulkUnpublish : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				_.each(selected, function(item) {
					var model = item.model;
					model.set('status', 'draft').save()
						.done(function()
						{
							alertify.success('Post has been unpublished');
						}).fail(function ()
						{
							alertify.error('Unable to unpublish post, please try again');
						});
				} );
			},

			/**
			 * Bulk delete selected posts
			 */
			bulkDelete : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				alertify.confirm('Are you sure you want to delete ' + selected.length + ' posts?', function(e)
				{
					if (e)
					{
						_.each(selected, function(item) {
							var model = item.model;
							model
								.destroy({wait : true})
								.done(function()
								{
									alertify.success('Post has been deleted');
								})
								.fail(function ()
								{
									alertify.error('Unable to delete post, please try again');
								});
						} );
					}
					else
					{
						alertify.log('Delete cancelled');
					}
				});
			},

			/**
			 * Select all posts
			 */
			selectAll : function ()
			{
				//e.preventDefault();

				var $el = this.$('.js-post-select-all-input');

				if ($el.is(':checked'))
				{
					this.children.each(function (child) { child.select(); });
					this.$('.select-text').addClass('visually-hidden');
					this.$('.unselect-text').removeClass('visually-hidden');
				}
				else
				{
					this.children.each(function (child) { child.unselect(); });
					this.$('.select-text').removeClass('visually-hidden');
					this.$('.unselect-text').addClass('visually-hidden');
				}
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state
				});

				return data;
			},

			showNextPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.lastPage <= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getNextPage();
				this.updatePagination();
			},
			showPreviousPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.firstPage >= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getPreviousPage();
				this.updatePagination();
			},
			showFirstPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.firstPage >= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getFirstPage();
				this.updatePagination();
			},
			showLastPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.lastPage <= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getLastPage();
				this.updatePagination();
			},
			showPage : function (e)
			{
				var $el = this.$(e.currentTarget),
						num = 0;

				e.preventDefault();

				_.each(
					$el.attr('class').split(' '),
					function (v) {
						if (v.indexOf('page-') === 0)
						{
							num = v.replace('page-', '');
						}
					}
				);
				this.collection.getPage(num -1);
				this.updatePagination();
			},

			updatePagination: function ()
			{
				this.$('.pagination').replaceWith(
					this.partialTemplates.pagination({
						pagination: this.collection.state
					})
				);
				this.$('.list-view-filter-info').html(
					this.partialTemplates.postListInfo({
						pagination: this.collection.state
					})
				);
			},
			updatePageSize : function (e)
			{
				e.preventDefault();
				var size = parseInt(this.$('#filter-posts-count').val(), 10);
				if (typeof size === 'number' && size > 0)
				{
					this.collection.setPageSize(size, {
						first: true
					});
				}
			},
			updatePostsSort : function (e)
			{
				e.preventDefault();
				var orderby = this.$('#filter-posts-sort').val();
				this.collection.setSorting(orderby);
				this.collection.getFirstPage();
			}
		});
	});
