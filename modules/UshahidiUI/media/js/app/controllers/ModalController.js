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

define(['App', 'backbone', 'marionette',
	'views/modals/CreatePostView', 'views/modals/EditPostView', 'views/modals/AddToSetView', 'views/modals/CreateSetView', 'views/modals/ChooseFormView',
	'models/PostModel'],
	function(App, Backbone, Marionette,
		PostCreateView, PostEditView, AddToSetView, CreateSetView, ChooseFormView,
		PostModel)
	{
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function(options)
			{
				// Store modal region we're controlling
				this.modal = options.modal;
				
				App.vent.on('post:create', this.postCreate, this);
				App.vent.on('post:edit', this.postEdit, this);
				App.vent.on('post:set', this.addToSet, this);
				App.vent.on('set:create', this.setCreate, this);
			},
			postCreate : function ()
			{
				var that = this,
					post = new PostModel({}),
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
							that.modal.show(new PostCreateView({
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
			},
			postEdit : function (post)
			{
				this.modal.show(new PostEditView({
					model : post
				}));
				this.modal.currentView.on('close', this.modal.close, this.modal);
			},
			addToSet : function (post)
			{
				this.modal.show(new AddToSetView({
					model : post
				}));
				this.modal.currentView.on('close', this.modal.close, this.modal);
			},
			setCreate : function (post)
			{
				this.modal.show(new CreateSetView({
					model : post
				}));
			}
		});
	});
