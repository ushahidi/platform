/**
 * Message List Item
 *
 * @module     MessageItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'alertify', 'underscore', 'jquery', 'drop',
		'views/EmptyView',
		'messages/list/ReplyView',
		'models/PostModel',
		'models/MessageModel',
		'hbs!messages/list/MessageListItem'
	],
	function(App, Marionette, alertify, _, $, Drop,
		EmptyView,
		ReplyView,
		PostModel,
		MessageModel,
		template
	)
	{
		//ItemView provides some default rendering logic
		return  Marionette.CompositeView.extend(
		{
			//Template HTML string
			template: template,
			tagName: 'li',
			className: 'list-view-message',
			events: {
				'click .js-message-archive': 'archiveMessage',
				'click .js-message-unarchive': 'unarchiveMessage',
				'click .js-message-create-post' : 'createPost',
				'click .js-message-activity' : 'toggleMessageActivity',
				'submit .js-message-post-reply-form' : 'replyMessage'
			},

			initialize: function()
			{
				this.collection = this.model.replies;
			},

			childView: ReplyView,

			emptyViewOptions:
			{
				emptyMessage: 'No activity on this message.'
			},

			emptyView: EmptyView,

			childViewContainer: 'ul.list-view-reply-list',

			modelEvents: {
				'sync': 'render'
			},

			onDomRefresh: function()
			{
				var that = this;

				this.actionsDrop = new Drop({
					target: this.$('.js-message-card-actions-drop')[0],
					content: this.$('.js-message-card-actions-drop-content')[0],
					classes: 'drop-theme-arrows',
					position: 'bottom center',
					openOn: 'click',
					remove: true
				});

				this.actionsDrop.on('open', function()
				{
					var $dropContent = $(this.content);
					$dropContent.off('.filter-drop')
						.on('click.filter-drop', '.js-message-card-action-activity', function(e)
						{
							that.actionsDrop.close();
							that.toggleMessageActivity.call(that, e.originalEvent);
						})
						.on('click.filter-drop', '.js-message-reply', function(e)
						{
							that.actionsDrop.close();
							that.toggleReply.call(that, e);
						})
						.on('click.filter-drop', '.js-message-create-post', function(e)
						{
							that.actionsDrop.close();
							that.createPost.call(that, e);
						})
						.on('click.filter-drop', '.js-message-archive', function(e)
						{
							that.actionsDrop.close();
							that.archiveMessage.call(that, e);
						})
						.on('click.filter-drop', '.js-message-unarchive', function(e)
						{
							that.actionsDrop.close();
							that.unarchiveMessage.call(that, e);
						})
						;
				});
			},

			archiveMessage : function(e)
			{
				e.preventDefault();

				this.model.set('status', 'archived')
					.save()
					.done(function()
					{
						alertify.success('Message has been archived');

						App.Collections.Messages.fetch();

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

						App.Collections.Messages.fetch();

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

			toggleReply: function(e)
			{
				e.preventDefault();
				this.$('.js-message-card-panel-reply').slideToggle(200);
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

			toggleMessageActivity : function(e)
			{
				e.preventDefault();
				this.$('.js-message-card-panel-activity').slideToggle(200);
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
				// @todo move to serializeModel?
				var data = _.extend(this.model.toJSON(), {
					isArchived : this.model.isArchived(),
					isIncoming : this.model.isIncoming()
				});
				return data;
			},
		});
	});
