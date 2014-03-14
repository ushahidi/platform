/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars','underscore', 'alertify', 'views/UserListItemView',
		'text!templates/UserList.html', 'text!templates/partials/pagination.html', 'text!templates/partials/user-list-info.html'],
	function( App, Marionette, Handlebars, _, alertify, UserListItemView,
		template, paginationTemplate, userListInfoTemplate)
	{
		Handlebars.registerPartial('pagination', paginationTemplate);
		Handlebars.registerPartial('user-list-info', userListInfoTemplate);

		return Marionette.CompositeView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			selectAllValue: false,
			// Lets just store the partial templates somewhere usefule
			partialTemplates :
			{
				pagination : Handlebars.compile(paginationTemplate),
				userListInfo : Handlebars.compile(userListInfoTemplate)
			},
			initialize: function()
			{
				// Bind select/unselect events from itemviews
				this.on('itemview:select', this.showHideBulkActions, this);
				this.on('itemview:unselect', this.showHideBulkActions, this);
			},

			itemView: UserListItemView,
			itemViewOptions: {},

			itemViewContainer: '.list-view-user-profile-list',

			events:
			{
				'click .js-page-first' : 'showFirstPage',
				'click .js-page-next' : 'showNextPage',
				'click .js-page-prev' : 'showPreviousPage',
				'click .js-page-last' : 'showLastPage',
				'click .js-page-change' : 'showPage',
				'change .js-filter-count' : 'updatePageSize',
				'change .js-filter-sort' : 'updateSort',
				'click .js-user-create' : 'showCreateUser',
				'click .js-user-bulk-delete' : 'bulkDelete',
				'click .js-user-bulk-change-role' : 'bulkChangeRole',
				'click .js-select-all' : 'selectAll'
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
			 * Show / Hide bulk actions toolbar when users are selected
			 */
			showHideBulkActions : function ()
			{
				var selected = this.getSelected();
				this.$('.js-bulk-action').toggleClass('disabled', selected.length > 0);
			},

			/**
			 * Bulk delete selected users
			 */
			bulkDelete : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				alertify.confirm('Are you sure you want to delete ' + selected.length + ' users?', function(e)
				{
					if (e)
					{
						_.each(selected, function(item) {
							var model = item.model;
							model
								.destroy({wait : true})
								.done(function()
								{
									alertify.success('User has been deleted');
									// Trigger a fetch. This is to remove the model from the listing and load another
									App.Collections.Users.fetch();
								})
								.fail(function ()
								{
									alertify.error('Unable to delete user, please try again');
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
			 * Bulk change role on selected users
			 */
			bulkChangeRole : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected(),
					$el = this.$(e.currentTarget),
					role,
					role_name;

				role = $el.attr('data-role-name'),
				role_name = $el.text();

				alertify.confirm('Are you sure you want to assign ' + selected.length + ' users the ' + role_name + ' role?', function(e)
				{
					if (e)
					{
						_.each(selected, function(item) {
							var model = item.model;
							model.set('role', role).save()
								.done(function()
								{
									alertify.success('User "' + model.get('username') + '" is now a '+ role_name);
								}).fail(function ()
								{
									alertify.error('Unable to change role, please try again');
								});
						} );
					}
					else
					{
						// cancelled
					}
				});
			},

			/**
			 * Select all users
			 */
			selectAll : function (e)
			{
				e.preventDefault();
				this.selectAllValue = ! this.selectAllValue;

				if (this.selectAllValue)
				{
					this.children.each(function (child) { child.select(); });
				}
				else
				{
					this.children.each(function (child) { child.unselect(); });
				}
				this.$('.select-text').toggleClass('visually-hidden', this.selectAllValue);
				this.$('.unselect-text').toggleClass('visually-hidden', ! this.selectAllValue);
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					roles: App.Collections.Roles.toJSON()
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
					this.partialTemplates.userListInfo({
						pagination: this.collection.state
					})
				);
				// @todo update counts next to roles
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
				this.collection.setSorting(orderby);
				this.collection.getFirstPage();
			},
			showCreateUser : function (e)
			{
				e.preventDefault();
				App.vent.trigger('user:create', this.model);
			}
		});
	});
