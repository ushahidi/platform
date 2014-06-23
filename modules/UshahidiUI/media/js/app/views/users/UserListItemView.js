/**
 * User List Item View
 *
 * @module     UserListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'alertify', 'hbs!templates/users/UserListItem'],
	function(App, Marionette, _, alertify, template)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			template: template,
			tagName: 'li',
			className: 'list-view-user',
			// Value to track if checkbox for this item has been selected
			selected : false,
			events : {
				'change .js-select-input' : 'updatedSelected',
				'click .js-user-delete': 'deleteUser',
				'click .js-user-edit' : 'showEditUser',
				'click .js-user-change-role' : 'changeRole'
			},

			modelEvents: {
				'sync': 'render'
			},

			serializeData : function ()
			{
				return _.extend(this.model.toJSON(), {
					roles: App.Collections.Roles.toJSON(),
					selected : this.selected
				});
			},

			deleteUser: function(e)
			{
				var that = this;
				e.preventDefault();
				alertify.confirm('Are you sure you want to delete this user ?', function(e)
				{
					if (e)
					{
						that.model.destroy({
							// Wait till server responds before destroying model
							wait: true
						}).done(function()
						{
							alertify.success('User has been deleted');
							// Trigger a fetch. This is to remove the model from the listing and load another
							App.Collections.Users.fetch();
						}).fail(function ()
						{
							alertify.error('Unable to delete user, please try again');
						});
					}
					else
					{
						alertify.log('Delete cancelled');
					}
				});
			},

			changeRole: function(e)
			{
				e.preventDefault();
				var that = this,
					$el = that.$(e.currentTarget),
					role = $el.attr('data-role-name'),
					role_name = $el.text();

				alertify.confirm('Are you sure you want to assign this user the '+ role_name + ' role?' , function(e)
				{
					if(e)
					{
						that.model.set('role',role).save()
							.done(function()
							{
								alertify.success('User "' + that.model.get('username') + '" is now a '+ role_name);
							}).fail(function()
							{
								alertify.error('Unable to change role, please try again');
							});
					}
					else
					{
						alertify.log('Role change cancelled');
					}
				});
			},

			showEditUser : function (e)
			{
				e.preventDefault();
				App.vent.trigger('user:edit', this.model);
			},

			/**
			* Select this item (for bulk actions)
			*/
			select : function ()
			{
				this.selected = true;
				this.$('.js-select-input').prop('checked', true);
				this.trigger('select');
			},

			/**
			* Unselect this item (for bulk actions)
			*/
			unselect : function ()
			{
				this.selected = false;
				this.$('.js-select-input').prop('checked', false);
				this.trigger('unselect');
			},

			updatedSelected : function (e)
			{
				var $el = this.$(e.currentTarget);
				this.selected = $el.is(':checked');
				this.trigger(this.selected ? 'select' : 'unselect');

				$el.parent()
					.toggleClass('selected-button', this.selected);
			}
		});
	});
