/**
 * User List Item View
 *
 * @module     UserListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App','handlebars', 'marionette', 'alertify', 'text!templates/UserListItem.html'],
	function(App,Handlebars, Marionette, alertify, template)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post',

			events: {
				'click .js-user-delete': 'deleteUser',
				'click .js-user-edit' : 'showEditUser',
			},

			initialize: function()
			{
				// Refresh this view when there is a change in this model
				this.listenTo(this.model,'change', this.render);
			},

			modelEvent: {
				'sync': 'render'
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

			showEditUser : function (e)
			{
				e.preventDefault();
				App.vent.trigger('user:edit', this.model);
			},
		});
	});
