/**
 * Message List Item
 *
 * @module     MessageItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'alertify', 'underscore', 'models/PostModel', 'hbs!templates/messages/MessageListItem'],
	function(App, Marionette, alertify, _, PostModel, template)
	{
		//ItemView provides some default rendering logic
		return  Marionette.ItemView.extend(
		{
			//Template HTML string
			template: template,
			tagName: 'li',
			className: 'list-view-message',

			events: {
				'click .js-message-archive': 'archiveMessage',
				'click .js-message-unarchive': 'unarchiveMessage',
				'click .js-message-create-post' : 'createPost',
				'click .js-message-view-post' : 'viewPost'
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

			createPost : function(e)
			{
				e.preventDefault();

				var that = this,
					post;

				post = new PostModel();
				post.url = this.model.url() + '/post';

				post.save()
					.done(function ()
					{
						alertify.success('Post has been created');
						that.model.fetch();
					}).fail(function ()
					{
						alertify.success('Unable to create post, please try again');
					});
			},

			viewPost : function(e)
			{
				e.preventDefault();
				alertify.confirm('View the post and lose any unsaved changes?', function(okay) {
					if (okay) {
						var hash = e.target.hash.substr(1);
						window.location.hash = hash;
					}
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
