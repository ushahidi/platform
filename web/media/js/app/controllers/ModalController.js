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

define(['App', 'marionette', 'backbone'],
	function(App, Marionette, Backbone)
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
				App.vent.on('customform:edit', this.customFormEdit, this);
				App.vent.on('customform:create', this.customFormCreate, this);
				App.vent.on('formgroup:edit', this.formGroupEdit, this);
				App.vent.on('formgroup:create', this.formGroupCreate, this);
			},
			postCreate : function ()
			{
				var that = this,
					prevUrl = Backbone.history.getFragment();

				Backbone.history.navigate('posts/create');

				require(['views/modals/ChooseFormView', 'views/modals/CreatePostView', 'models/PostModel'],
					function(ChooseFormView, CreatePostView, PostModel)
				{
					var post = new PostModel({}),
						chooseView;

					// Reload forms before render
					App.Collections.Forms.fetch()
						.done(function () {
							chooseView = new ChooseFormView({
								model: post,
								forms: App.Collections.Forms
							}).on('form:select', function ()
								{
									that.modal.show(new CreatePostView({
										model: post
									}));
								}
							);

							that.modal.once('modal:close', function () {
								Backbone.history.navigate(prevUrl);
							});

							that.modal.show(chooseView);
						});
				});
			},
			postEdit : function (post)
			{
				var that = this;

				require(['views/modals/EditPostView'],
					function(EditPostView)
				{
					post.fetchRelations(true).done(function()
					{
						that.modal.show(new EditPostView({
							model : post
						}));
					});
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
				});
			},
			customFormEdit : function (form)
			{
				var that = this;

				require(['form-manager/EditCustomFormView'],
					function(EditCustomFormView)
				{
					that.modal.show(new EditCustomFormView({
						model : form
					}));
				});
			},
			customFormCreate : function ()
			{
				var that = this;

				require(['form-manager/EditCustomFormView', 'models/FormModel'],
					function(EditCustomFormView, FormModel)
				{
					var form = new FormModel({});

					that.modal.show(new EditCustomFormView({
						model : form
					}));
				});
			},
			formGroupEdit : function (group)
			{
				var that = this;

				require(['form-manager/EditFormGroupView'],
					function(EditFormGroupView)
				{
					that.modal.show(new EditFormGroupView({
						model : group
					}));
				});
			},
			formGroupCreate : function (form_id, groupCollection)
			{
				var that = this;

				require(['form-manager/EditFormGroupView', 'models/FormGroupModel'],
					function(EditFormGroupView, FormGroupModel)
				{
					var group = new FormGroupModel({
						form_id : form_id
					});

					that.modal.show(new EditFormGroupView({
						model : group,
						collection: groupCollection
					}));
				});
			}
		});
	});
