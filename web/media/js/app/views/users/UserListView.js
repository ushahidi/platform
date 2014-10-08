/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'jquery', 'alertify', 'drop',
		'views/users/UserListItemView',
		'views/EmptyView',
		'hbs!templates/users/UserList',
		'mixin/PageableViewBehavior'
	],
	function( App, Marionette, _, $, alertify, Drop,
		UserListItemView,
		EmptyView,
		template,
		PageableViewBehavior
	)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,
			modelName: 'users',
			selectAllValue: false,
			initialize: function()
			{
				// Bind select/unselect events from childviews
				this.on('childview:select', this.showHideBulkActions, this);
				this.on('childview:unselect', this.showHideBulkActions, this);
			},

			onDomRefresh: function()
			{
				var that = this;

				this.$('.js-user-bulk-change-role-drop').each(function()
				{
					var bulkRoleDrop = new Drop({
						target: $(this)[0],
						content: $(this).siblings('.js-user-bulk-change-role-drop-content')[0],
						classes: 'drop-theme-arrows',
						position: 'left middle',
						openOn: 'click',
						remove: true
					});

					bulkRoleDrop.on('open', function()
					{
						$(this.content).off('.bulk-role-drop')
							.on('click.bulk-role-drop', '.js-user-bulk-change-role', function(e)
							{
								that.bulkChangeRole.call(that, e.originalEvent);
								that.bulkChangeRole.close();
							})
							;
					});
				});

				this.actionsDrop = new Drop({
					target: this.$('.js-user-bulk-actions-drop')[0],
					content: this.$('.js-user-bulk-actions-drop-content')[0],
					classes: 'drop-theme-arrows',
					position: 'bottom right',
					openOn: 'click',
					remove: true
				});

				this.actionsDrop.on('open', function()
				{
					var $dropContent = $(this.content);
					$dropContent.off('.actions-drop')
						.on('click.actions-drop', '.js-user-bulk-select-all', function()
						{
							that.selectAll();
							that.actionsDrop.close();
							$(this).closest('li').addClass('none');
							$dropContent.find('.js-user-bulk-unselect-all').closest('li').removeClass('none');
						})
						.on('click.actions-drop', '.js-user-bulk-unselect-all', function()
						{
							that.unselectAll();
							that.actionsDrop.close();
							$(this).closest('li').addClass('none');
							$dropContent.find('.js-user-bulk-select-all').closest('li').removeClass('none');
						})
						.on('click.actions-drop', '.js-user-create', function(e)
						{
							that.actionsDrop.close();
							that.showCreateUser.call(that, e);
						})
						.on('click.actions-drop', '.js-user-bulk-delete', function(e)
						{
							that.actionsDrop.close();
							that.bulkDelete.call(that, e.originalEvent);
						})
						;
				});

				this.roleFilterDrop = new Drop({
					target: this.$('.js-user-filter-role-drop')[0],
					content: this.$('.js-user-filter-role-drop-content')[0],
					classes: 'drop-theme-arrows',
					position: 'bottom center',
					openOn: 'click',
					remove: true
				});

				this.roleFilterDrop.on('open', function()
				{
					$(this.content).off('.role-drop')
						.on('click.role-drop', '.js-user-filter-role', function(e)
						{
							that.filterByRole.call(that, e);
							that.roleFilterDrop.close();
						})
						;
				});
			},

			childView: UserListItemView,

			emptyViewOptions:
			{
				emptyMessage: 'No users found.',
			},

			childViewContainer: '.list-view-user-profile-list',

			emptyView: EmptyView,

			events:
			{
				'click .js-user-create' : 'showCreateUser',
				'click .js-user-bulk-delete' : 'bulkDelete',
				'click .js-user-bulk-change-role' : 'bulkChangeRole',
				'click .js-select-all' : 'toggleSelectAll',
				'submit .js-user-search-form' : 'searchUsers',
				'click .js-user-filter-role' : 'filterByRole',
			},

			collectionEvents :
			{
				request: 'showLoading unselectAll',
				sync : 'hideLoading'
			},

			behaviors: {
				PageableViewBehavior: {
					behaviorClass : PageableViewBehavior,
					modelName : 'users'
				}
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
				$(this.actionsDrop.content).find('.js-bulk-action')
					.toggleClass('disabled', selected.length === 0);
				this.$('.js-bulk-action')
					.toggleClass('disabled', selected.length === 0);
			},

			/**
			 * Bulk delete selected users
			 */
			bulkDelete : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				if (selected.length === 0)
				{
					return;
				}

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
					$el = $(e.target),
					role,
					role_name;

				role = $el.attr('data-role-name'),
				role_name = $el.text();

				if (selected.length === 0)
				{
					return;
				}

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

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					pageSizes: this.collection.pageSizes,
					sortKeys: this.collection.sortKeys,
					roles: App.Collections.Roles.toJSON(),
					modelName : this.modelName
				});
				return data;
			},
			showCreateUser : function (e)
			{
				e.preventDefault();
				App.vent.trigger('user:create', this.model);
			},
			searchUsers : function(e)
			{
				e.preventDefault();

				var keyword = this.$('.js-user-search-input').val();

				App.Collections.Users.setFilterParams({
					q : keyword
				});
			},
			filterByRole : function(e)
			{
				e.preventDefault();

				var $el = $(e.currentTarget),
					role = $el.data('role-name'),
					params = App.Collections.Users.setFilterParams({
						role : role
					});

				$el.closest('.js-filter-tags-list')
					.find('li')
						.removeClass('active')
						.find('.role-title > span').addClass('visually-hidden')
						.end()
					.filter('li[data-role-name="' + role + '"]')
						.addClass('active')
						.find('.role-title > span').removeClass('visually-hidden');

				this.$('.js-user-search-input').val(params.q);
			},

			showLoading : function()
			{
				// Hide the ul li
				this.$('.list-view-user-profile-list').addClass('visually-hidden');

				// Show the loading text
				this.$('.list-view-wrapper p.js-loading').removeClass('visually-hidden');
			},

			hideLoading : function()
			{
				// Hide the loading text
				this.$('.list-view-wrapper p.js-loading').addClass('visually-hidden');

				// Show the ul li
				this.$('.list-view-user-profile-list').removeClass('visually-hidden');
			}
		});
	});
