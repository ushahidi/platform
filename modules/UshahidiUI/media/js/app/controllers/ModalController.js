/**
 * Modal Controller
 *
 * Handles showing/hiding views in the modal region
 *
 * @module     ModalController
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette'],
	function(App, Marionette)
	{
		return Marionette.Controller.extend(
		{
			initialize : function(options)
			{
				// Store modal region we're controlling
				this.modal = options.modal;

				App.vent.on('post:create', this.postCreate, this);
				App.vent.on('post:edit', this.postEdit, this);
				App.vent.on('post:set', this.addToSet, this);
				App.vent.on('set:create', this.setCreate, this);
				App.vent.on('user:edit', this.userEdit, this);
				App.vent.on('user:create',this.userCreate, this);
				App.vent.on('tag:edit', this.tagEdit, this);
				App.vent.on('tag:create', this.tagCreate, this);
			},
			postCreate : function ()
			{
				var that = this;

				require(['views/modals/ChooseFormView', 'views/modals/CreatePostView', 'models/PostModel'],
					function(ChooseFormView, CreatePostView, PostModel)
				{
					var post = new PostModel({}),
							chooseView;

					chooseView = new ChooseFormView({
						model: post,
						forms: App.Collections.Forms
					}).on('form:select', function ()
						{
							// @todo ensure tagscollection is loaded

							// @todo move this event handling to modal region
							that.modal.currentView.on('modal:closed', function ()
							{
								that.modal.show(new CreatePostView({
									model: post
								}));
								that.modal.currentView.on('close', that.modal.close, that.modal);
								// Unbind fn
								this.off('modal:closed');
							});
							that.modal.close();
						}
					);

					that.modal.show(chooseView);
				});
			},
			postEdit : function (post)
			{
				var that = this;

				require(['views/modals/EditPostView'],
					function(EditPostView)
				{
					post.relationsCallback.done(function()
					{
						that.modal.show(new EditPostView({
							model : post
						}));
						that.modal.currentView.on('close', that.modal.close, that.modal);
					});
					post.fetchRelations();
				});
			},
			addToSet : function (post)
			{
				var that = this;

				require(['views/modals/AddToSetView'],
					function(AddToSetView)
				{
					that.modal.show(new AddToSetView({
						model : post
					}));
					that.modal.currentView.on('close', that.modal.close, that.modal);
				});
			},
			setCreate : function (post)
			{
				var that = this;

				require(['views/modals/CreateSetView'],
					function(CreateSetView)
				{
					that.modal.show(new CreateSetView({
						model : post
					}));
				});
			},
			userEdit : function (user)
			{
				var that = this;

				require(['views/modals/EditUserView'],
					function(EditUserView)
				{
					that.modal.show(new EditUserView({
						model : user
					}));
					that.modal.currentView.on('close', that.modal.close, that.modal);
				});
			},
			userCreate : function ()
			{
				var that = this;

				require(['views/modals/EditUserView', 'models/UserModel'],
					function(EditUserView,UserModel)
				{
					var user = new UserModel({});
					that.modal.show(new EditUserView({
						model : user
					}));
					that.modal.currentView.on('close', that.modal.close, that.modal);
				});
			},
			tagEdit : function (tag)
			{
				var that = this;

				require(['views/modals/EditTagView'],
					function(EditTagView)
				{
					that.modal.show(new EditTagView({
						model : tag
					}));
					that.modal.currentView.on('close', that.modal.close, that.modal);
				});
			},
			tagCreate : function ()
			{
				var that = this;

				require(['views/modals/EditTagView', 'models/TagModel'],
					function(EditTagView,TagModel)
				{
					var tag = new TagModel({});
					that.modal.show(new EditTagView({
						model : tag
					}));
					that.modal.currentView.on('close', that.modal.close, that.modal);
				});
			}
		});
	});
