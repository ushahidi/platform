/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'underscore', 'jquery', 'drop', 'alertify',
		'views/ListView',
		'views/users/UserListItemView',
		'hbs!templates/users/UserList',
		'mixin/PageableViewBehavior',
		'mixin/SelectableListBehavior'
	],
	function( App, _, $, Drop, alertify,
		ListView,
		UserListItemView,
		template
	)
	{
		return ListView.extend(
		{
			template: template,
			modelName: 'user',

			behaviors: {
				PageableView: {
					modelName: 'users'
				}
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
								bulkRoleDrop.close();
							});
					});
				});

				ListView.prototype.onDomRefresh.call(this);

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

			events: _.extend(ListView.prototype.events, {
				'click .js-user-bulk-change-role' : 'bulkChangeRole',
				'submit .js-user-search-form' : 'searchUsers',
				'click .js-user-filter-role' : 'filterByRole',
			}),

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

			serializeData : function ()
			{
				return _.extend(ListView.prototype.serializeData.call(this), {
					roles: App.Collections.Roles.toJSON()
				});
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
			}
		});
	});
