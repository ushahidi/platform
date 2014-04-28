/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'underscore', 'alertify', 'views/TagListItemView', 'views/EmptyView', 'text!templates/TagList.html'],
	function( App, Marionette, Handlebars, _, alertify, TagListItemView, EmptyView, template)
	{
		return Marionette.CompositeView.extend(
		{
			template: Handlebars.compile(template),
			modelName: 'tags',

			initialize: function ()
			{
				// Bind select/unselect events from itemviews
				this.on('itemview:select', this.showHideBulkActions, this);
				this.on('itemview:unselect', this.showHideBulkActions, this);
			},

			itemView: TagListItemView,

			itemViewContainer: '.list-view-tag-list',

			itemViewOptions:
			{
				emptyMessage: 'No tags found.',
			},

			emptyView: EmptyView,

			events:
			{
				'click .js-page-first' : 'showFirstPage',
				'click .js-page-next' : 'showNextPage',
				'click .js-page-prev' : 'showPreviousPage',
				'click .js-page-last' : 'showLastPage',
				'click .js-page-change' : 'showPage',
				'change .js-filter-count' : 'updatePageSize',
				'change .js-filter-sort' : 'updateSort',
				'click .js-tag-create' : 'showCreateTag',
				'click .js-tag-bulk-delete' : 'bulkDelete',
				'click .js-select-all' : 'toggleSelectAll'
			},

			collectionEvents :
			{
				reset : 'updatePagination unselectAll',
				add : 'updatePagination',
				remove : 'updatePagination',
				request : 'unselectAll',
			},

			getSelected : function ()
			{
				return this.children.filter('selected');
			},

			/**
			 * Show / Hide bulk actions toolbar when users are selected
			 */
			showHideBulkActions : function ()
			{
				var selected = this.getSelected();
				this.$('.js-bulk-action').toggleClass('disabled', selected.length === 0);
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					sortKeys: this.collection.sortKeys,
					modelName : this.modelName
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
				this.$('.js-pagination').replaceWith(
					Handlebars.partials.pagination({
						pagination: this.collection.state
					})
				);
				this.$('.js-list-view-filter-info').replaceWith(
					Handlebars.partials.listinfo({
						pagination: this.collection.state,
						modelName: this.modelName
					})
				);
			},
			updatePageSize : function (e)
			{
				e.preventDefault();
				var size = parseInt(this.$('.js-filter-count').val(), 10);
				if (typeof size === 'number' && size > 0)
				{
					this.collection.setPageSize(size, {
						first: true
					});
				}
			},
			updateSort : function (e)
			{
				e.preventDefault();
				var orderby = this.$('.js-filter-sort').val();
				if (orderby === 'tag')
				{
					this.collection.setSorting(orderby, -1);
				}
				else
				{
					this.collection.setSorting(orderby);
				}
				this.collection.fullCollection.sort();
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

				if (selected.length === 0)
				{
					return;
				}

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
									// Trigger a fetch. This is to remove the model from the listing and load another
									App.Collections.Tags.fetch();
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

			/**
			 * Select all users
			 */
			toggleSelectAll : function (e, select)
			{
				_.result(e, 'preventDefault');

				this.selectAllValue = (typeof select !== 'undefined') ? select : ! this.selectAllValue;

				if (this.selectAllValue)
				{
					this.children.each(function (child) { _.result(child, 'select'); });
				}
				else
				{
					this.children.each(function (child) { _.result(child, 'unselect'); });
				}
				this.$('.select-text').toggleClass('visually-hidden', this.selectAllValue);
				this.$('.unselect-text').toggleClass('visually-hidden', ! this.selectAllValue);
			},

			selectAll : function(e)
			{
				this.toggleSelectAll(e, true);
			},

			unselectAll : function (e)
			{
				this.toggleSelectAll(e, false);
			},
		});
	});
