/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars','underscore', 'alertify', 'views/TagListItemView',
		'text!templates/TagList.html', 'text!templates/partials/pagination.html', 'text!templates/partials/post-list-info.html'],
	function( App, Marionette, Handlebars, _, alertify, TagListItemView,
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

			initialize: function ()
			{
				// Bind select/unselect events from itemviews
				this.on('itemview:select', this.showHideBulkActions, this);
				this.on('itemview:unselect', this.showHideBulkActions, this);
			},

			itemView: TagListItemView,
			itemViewOptions: {},

			itemViewContainer: '.list-view-tag-list',

			events:
			{
				'click .js-list-view-select-post' : 'showHideBulkActions',
				'click .js-page-first' : 'showFirstPage',
				'click .js-page-next' : 'showNextPage',
				'click .js-page-prev' : 'showPreviousPage',
				'click .js-page-last' : 'showLastPage',
				'click .js-page-change' : 'showPage',
				'change #filter-posts-count' : 'updatePageSize',
				'change #filter-posts-sort' : 'updatePostsSort',
				'click .js-tag-create' : 'showCreateTag',
				'click .js-tag-bulk-delete' : 'bulkDelete',
				'change .js-tag-select-all' : 'selectAll'
			},

			collectionEvents :
			{
				reset : 'updatePagination',
				add : 'updatePagination',
				remove : 'updatePagination'
			},

			getSelected : function ()
			{
				return this.children.filter('selected');
			},

			showHideBulkActions : function ()
			{
				var $checked = this.$('.js-list-view-select-post input[type="checkbox"]:checked');

				if ($checked.length > 0)
				{
					this.$('.js-list-view-bulk-actions').removeClass('hidden');
					this.$('.js-list-view-bulk-actions').addClass('visible');
				}
				else
				{
					this.$('.js-list-view-bulk-actions').removeClass('visible');
					this.$('.js-list-view-bulk-actions').addClass('hidden');
				}
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					sortKeys: this.collection.sortKeys
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
			},
			showCreateTag : function (e)
			{
				e.preventDefault();
				App.vent.trigger('tag:create', this.model);
			},

			bulkDelete : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				alertify.confirm('Are you sure you want to delete ' + selected.length + ' tags?', function(e)
				{
					if (e)
					{
						_.each(selected, function(item) {
						var model = item.model;
						model
								.destroy({wait : true})
								.done(function()
								{
									alertify.success('Tag has been deleted');
								})
								.fail(function ()
								{
									alertify.error('Unable to delete tag, please try again');
							});
						} );
					}
					else
					{
						alertify.log('Delete cancelled');
					}
				});
			},

			selectAll : function ()
			{
				//e.preventDefault();
				var $el = this.$('.js-tag-select-all-input');
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
			}
		});
	});