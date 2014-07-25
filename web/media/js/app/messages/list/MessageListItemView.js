/**
 * Message List Item
 *
 * @module     MessageItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'alertify', 'underscore',
		'models/PostModel',
		'models/MessageModel',
		'hbs!messages/list/MessageListItem'
	],
	function(App, Marionette, alertify, _,
		PostModel,
		MessageModel,
		template
	)
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
				'click .js-message-view-post' : 'viewPost',
				'submit .js-message-post-reply-form' : 'replyMessage'
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

			replyMessage : function(e)
			{
				e.preventDefault();

				var that = this,
					message = that.$('.textarea').val(),
					outgoing = new MessageModel({
						message: message,
						parent_id : this.model.get('id'),
						status : 'pending',
						type : this.model.get('type'),
						direction : 'outgoing',
						data_provider : this.model.get('contact').data_provider,
						contact_id : this.model.get('contact').id
					});

				//Disable fields upon saving
				this.disableFields(that);

				outgoing.save()
					.done(function()
					{
						// Clear content of the textarea
						that.$('.textarea').val('');

						// Enable fields
						that.enableFields(that);

						alertify.success('Your message is queued to be sent.');
					})
					.fail(function()
					{
						// Enable fields
						that.enableFields(that);
						alertify.error('Unable to send message. Make sure you have entered a messages for the response. Please try again.');
					});
			},

			enableFields : function(that)
			{
				// Enable textarea
				that.$('.js-response-textarea').removeAttr('disabled');

				// Enable autofill location info button
				that.$('.js-location-autofill').removeAttr('disabled');

				// Enable autofill more info button
				that.$('.js-more-info-autofill').removeAttr('disabled');

				// Enable send button
				that.$('.js-message-post-reply').removeAttr('disabled');
			},

			disableFields : function(that)
			{
				// Disable textarea
				that.$('.js-response-textarea').attr('disabled', 'disabled');

				// Disable autofill location info button
				that.$('.js-location-autofill').attr('disabled', 'disabled');

				// Disable autofill more info button
				that.$('.js-more-info-autofill').attr('disabled', 'disabled');

				// Disable send button
				that.$('.js-message-post-reply').attr('disabled', 'disabled');
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
