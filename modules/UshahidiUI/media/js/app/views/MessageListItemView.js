/**
 * Message List Item
 *
 * @module     MessageItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['handlebars', 'marionette', 'text!templates/MessageListItem.html', 'alertify', 'underscore'],
	function(Handlebars, Marionette, template, alertify, _)
	{
		//ItemView provides some default rendering logic
		return  Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-message',

			events: {
				'click .js-message-archive': 'archiveMessage',
				'click .js-message-unarchive': 'unarchiveMessage',
			},

			modelEvents: {
				'sync': 'render'
			},

			archiveMessage : function(e)
			{
				e.preventDefault();

				this.model.set('status', 'archived')
					.save()
					.done(function()
					{
						alertify.success('Message has been archived');
					}).fail(function ()
					{
						alertify.error('Unable to archive message, please try again');
					});
			},

			unarchiveMessage : function(e)
			{
				e.preventDefault();

				this.model.set('status', 'received')
					.save()
					.done(function()
					{
						alertify.success('Message has been restored');
					}).fail(function ()
					{
						alertify.error('Unable to restore message, please try again');
					});
			},

			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					isArchived : this.model.isArchived(),
					isIncoming : this.model.isIncoming()
				});
				return data;
			}
		});
	});
